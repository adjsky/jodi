import { isSameDay, now, toCalendarDate, toTimeZone, toZoned } from "@internationalized/date";
import { RecurrenceExceptionRepository } from "#entities/recurrence/model/recurrence-exception-repository.ts";
import { RecurrenceExpander } from "#entities/recurrence/model/recurrence-expander.ts";
import { TIMEZONE, UTC } from "#shared/cfg/constants.ts";
import { db } from "#shared/db/dexie.ts";
import { live_query } from "#shared/db/live-query.svelte.ts";
import { raise } from "#shared/lib/exception/raise.ts";
import { isDeepEqual, keys, omit, omitBy, sortBy } from "remeda";
import { RRule } from "rrule";

import { CategoryRepository } from "./category-repository.ts";
import { TodoPositionRepository } from "./todo-position-repository.ts";
import { TodoRepository } from "./todo-repository.ts";

import type { Todo, TodoDraft, TodoOccurrence } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";
import type { RecurrenceScope } from "#entities/recurrence/model/types.ts";
import type { LiveQuery } from "#shared/db/live-query.svelte.ts";
import type { MaybeGetter } from "runed";

function list(date: MaybeGetter<CalendarDate>): LiveQuery<TodoOccurrence[]> {
    return live_query(date, async (date) => {
        const [todos, exceptions, positions, categories] = await db.transaction(
            "r",
            [db.todos, db.recurrence_exceptions, db.todo_positions, db.categories],
            async () => {
                const [todos, ...rest] = await Promise.all([
                    TodoRepository.list(date),
                    TodoPositionRepository.list(date),
                    CategoryRepository.list()
                ]);

                const exceptions = await RecurrenceExceptionRepository.list(
                    "todo",
                    todos.map((t) => t.id)
                );

                return [todos, exceptions, ...rest];
            }
        );

        const category = new Map(categories.map((c) => [c.id, c.name]));
        const position = new Map(positions.map((p) => [p.todo_id, p.position]));

        const expander = new RecurrenceExpander({
            start_key: "scheduled_at",
            end_key: null,
            shifted_date_keys: [],
            timezone: TIMEZONE
        });

        return sortBy(
            expander.on(date, todos, exceptions),
            (t) => (t.category_id ? (category.get(t.category_id) ?? "") : ""),
            (t) => position.get(t.id) ?? Infinity,
            (t) => t.created_at.toDate().getTime()
        );
    });
}

async function create(draft: TodoDraft): Promise<string> {
    const id = await TodoRepository.create(draft);
    return id;
}

async function update(
    id: string,
    draft: TodoDraft,
    occurs_at: CalendarDate | null,
    scope: RecurrenceScope
): Promise<void> {
    if (scope === "following") {
        throw new Error('Scope "following" is not supported for update.');
    }

    async function update_single_occurrence(todo: Todo): Promise<void> {
        if (!occurs_at) raise("No occurs_at.");

        const existing = await RecurrenceExceptionRepository.find("todo", todo.id, occurs_at);
        const current = {
            ...todo,
            ...existing?.overrides,
            scheduled_at:
                existing?.overrides.scheduled_at ??
                toTimeZone(todo.scheduled_at, UTC).set(occurs_at)
        };
        const overrides = omitBy(omit(draft, ["rrule", "completed_at"]), (value, key) => {
            if (key === "scheduled_at") {
                return draft.scheduled_at.compare(current.scheduled_at) === 0;
            }

            return value === current[key];
        });

        const current_local_date = toCalendarDate(toTimeZone(current.scheduled_at, TIMEZONE));
        const next_local_date = toCalendarDate(toTimeZone(draft.scheduled_at, TIMEZONE));

        if (
            draft.category_id !== current.category_id ||
            !isSameDay(next_local_date, current_local_date)
        ) {
            await TodoPositionRepository.destroy_for_date(todo.id, current_local_date);
        }

        await RecurrenceExceptionRepository.upsert({
            id: existing?.id ?? crypto.randomUUID(),
            recurrenceable_type: "todo",
            recurrenceable_id: todo.id,
            occurs_at: occurs_at.toString(),
            is_cancelled: existing?.is_cancelled ?? false,
            overrides: { ...existing?.overrides, ...overrides }
        });
    }

    async function update_todo(todo: Todo): Promise<void> {
        const attributes = { ...draft };

        if (scope === "all" && todo.rrule !== null && occurs_at !== null) {
            await RecurrenceExceptionRepository.reset(
                "todo",
                todo.id,
                keys(omit(draft, ["rrule", "completed_at"]))
            );

            const scheduled_at = toTimeZone(draft.scheduled_at, UTC);

            if (isSameDay(scheduled_at, occurs_at)) {
                const series_start_date = toCalendarDate(toTimeZone(todo.scheduled_at, UTC));

                attributes.scheduled_at = toTimeZone(
                    scheduled_at.set(series_start_date),
                    draft.scheduled_at.timeZone
                );
            }

            attributes.rrule = await split_recurring_todo(todo, todo.rrule);
        }

        await reset_positions(todo);
        await TodoRepository.update(todo.id, attributes);
    }

    async function split_recurring_todo(todo: Todo, rrule: string): Promise<string> {
        if (
            draft.rrule !== null &&
            isDeepEqual(RRule.parseString(rrule), RRule.parseString(draft.rrule))
        ) {
            return draft.rrule;
        }

        const split_date = toCalendarDate(toTimeZone(draft.scheduled_at, UTC));
        const truncated_rrule = new RRule({
            ...RRule.parseString(rrule),
            count: null,
            until: toZoned(split_date, UTC).subtract({ seconds: 1 }).toDate()
        }).toString();

        const new_id = await TodoRepository.create({ ...todo, ...draft });

        if (draft.rrule === null) {
            await RecurrenceExceptionRepository.destroy("todo", todo.id, split_date);
        } else {
            await RecurrenceExceptionRepository.transfer("todo", todo.id, new_id, split_date);
        }

        await TodoPositionRepository.transfer(todo.id, new_id, split_date);
        await reset_positions({ ...todo, ...draft, id: new_id });

        return truncated_rrule;
    }

    async function reset_positions(todo: Todo): Promise<void> {
        const previous_date = occurs_at ?? toCalendarDate(toTimeZone(todo.scheduled_at, UTC));

        if (
            todo.category_id !== draft.category_id ||
            !isSameDay(toTimeZone(draft.scheduled_at, UTC), previous_date)
        ) {
            await TodoPositionRepository.destroy_all(todo.id);
        }
    }

    await db.transaction(
        "rw",
        [db.todos, db.recurrence_exceptions, db.todo_positions],
        async () => {
            const todo = await TodoRepository.find(id);
            if (!todo) return;

            if (scope === "this" && todo.rrule !== null) {
                await update_single_occurrence(todo);
            } else {
                await update_todo(todo);
            }
        }
    );
}

async function destroy(
    id: string,
    occurs_at: CalendarDate | null,
    date: CalendarDate | null,
    scope: RecurrenceScope
): Promise<void> {
    async function destroy_following(rrule: string) {
        if (!occurs_at || !date) raise("No occurs_at and date.");

        await TodoRepository.update(id, {
            rrule: new RRule({
                ...RRule.parseString(rrule),
                count: null,
                until: toZoned(date, UTC).subtract({ seconds: 1 }).toDate()
            }).toString()
        });
        await RecurrenceExceptionRepository.destroy("todo", id, date);
        await TodoPositionRepository.destroy_from_date(id, date);
        const existing = await RecurrenceExceptionRepository.find("todo", id, occurs_at);
        await RecurrenceExceptionRepository.upsert({
            id: existing?.id ?? crypto.randomUUID(),
            recurrenceable_type: "todo",
            recurrenceable_id: id,
            occurs_at: occurs_at.toString(),
            is_cancelled: true,
            overrides: {}
        });
    }

    async function destroy_this() {
        if (!occurs_at || !date) raise("No occurs_at and date.");

        const existing = await RecurrenceExceptionRepository.find("todo", id, occurs_at);
        await RecurrenceExceptionRepository.upsert({
            id: existing?.id ?? crypto.randomUUID(),
            recurrenceable_type: "todo",
            recurrenceable_id: id,
            occurs_at: occurs_at.toString(),
            is_cancelled: true,
            overrides: {}
        });
        await TodoPositionRepository.destroy_for_date(id, date);
    }

    async function destroy() {
        await RecurrenceExceptionRepository.destroy("todo", id);
        await TodoPositionRepository.destroy_all(id);
        await TodoRepository.destroy(id);
    }

    await db.transaction(
        "rw",
        [db.todos, db.recurrence_exceptions, db.todo_positions],
        async () => {
            const todo = await TodoRepository.find(id);
            if (!todo) return;

            if (scope === "following" && todo.rrule !== null) {
                await destroy_following(todo.rrule);
            } else if (scope === "this" && todo.rrule !== null) {
                await destroy_this();
            } else {
                await destroy();
            }
        }
    );
}

async function complete(id: string, occurs_at: CalendarDate | null): Promise<void> {
    await db.transaction("rw", [db.todos, db.recurrence_exceptions], async () => {
        const todo = await TodoRepository.find(id);
        if (!todo) return;

        if (todo.rrule !== null && occurs_at !== null) {
            const existing = await RecurrenceExceptionRepository.find("todo", id, occurs_at);

            await RecurrenceExceptionRepository.upsert({
                id: existing?.id ?? crypto.randomUUID(),
                recurrenceable_type: "todo",
                recurrenceable_id: id,
                occurs_at: occurs_at.toString(),
                is_cancelled: existing?.is_cancelled ?? false,
                overrides: {
                    ...existing?.overrides,
                    completed_at: existing?.overrides.completed_at ? null : now(UTC)
                }
            });
        } else {
            await TodoRepository.update(id, {
                completed_at: todo.completed_at ? null : now(UTC)
            });
        }
    });
}

export const TodoService = {
    list,
    create,
    update,
    destroy,
    complete
};
