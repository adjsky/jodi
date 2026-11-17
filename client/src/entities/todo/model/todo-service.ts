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
import type { CalendarDate, ZonedDateTime } from "@internationalized/date";
import type { RecurrenceException, RecurrenceScope } from "#entities/recurrence/model/types.ts";
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

    type TodoException = Extract<RecurrenceException, { recurrenceable_type: "todo" }>;

    async function update_single_occurrence(todo: Todo): Promise<void> {
        if (!occurs_at) raise("No occurs_at.");

        const existing = await RecurrenceExceptionRepository.find("todo", id, occurs_at);
        const scheduled_at = toTimeZone(todo.scheduled_at, UTC).set(occurs_at);
        const attributes = omit(draft, ["rrule", "completed_at"]);
        const overrides = omitBy(attributes, (value, key) => {
            if (key === "scheduled_at") {
                return (
                    draft.scheduled_at.compare(existing?.overrides.scheduled_at ?? scheduled_at) ===
                    0
                );
            }

            const current =
                existing && Object.hasOwn(existing.overrides, key)
                    ? existing.overrides[key]
                    : todo[key];

            return value === current;
        });

        await reset_occurrence_positions(todo, scheduled_at, existing);

        await RecurrenceExceptionRepository.upsert({
            id: existing?.id ?? crypto.randomUUID(),
            recurrenceable_type: "todo",
            recurrenceable_id: id,
            occurs_at: occurs_at.toString(),
            is_cancelled: existing?.is_cancelled ?? false,
            overrides: { ...existing?.overrides, ...overrides }
        });
    }

    async function reset_occurrence_positions(
        todo: Todo,
        scheduled_at: ZonedDateTime,
        existing: TodoException | null
    ): Promise<void> {
        const current_scheduled_at = existing?.overrides.scheduled_at ?? scheduled_at;
        const current_category =
            existing && Object.hasOwn(existing.overrides, "category_id")
                ? existing.overrides.category_id
                : todo.category_id;

        if (
            draft.category_id !== current_category ||
            !isSameDay(
                toTimeZone(draft.scheduled_at, TIMEZONE),
                toTimeZone(current_scheduled_at, TIMEZONE)
            )
        ) {
            await TodoPositionRepository.destroy_for_date(
                id,
                toCalendarDate(toTimeZone(current_scheduled_at, TIMEZONE))
            );
        }
    }

    async function update_todo(todo: Todo): Promise<void> {
        const attributes = { ...draft };

        if (scope === "all" && todo.rrule !== null && occurs_at !== null) {
            await RecurrenceExceptionRepository.reset(
                "todo",
                id,
                keys(omit(draft, ["rrule", "completed_at"]))
            );
            normalize_recurring_data_for_update(todo, attributes, occurs_at);
            await split_recurring_todo(todo, todo.rrule, attributes);
        }

        await reset_positions(todo);
        await TodoRepository.update(id, attributes);
    }

    function normalize_recurring_data_for_update(
        todo: Todo,
        attributes: TodoDraft,
        date: CalendarDate
    ): void {
        const scheduled_at = toTimeZone(attributes.scheduled_at, UTC);

        if (isSameDay(scheduled_at, date)) {
            attributes.scheduled_at = toTimeZone(
                scheduled_at.set(toCalendarDate(toTimeZone(todo.scheduled_at, UTC))),
                draft.scheduled_at.timeZone
            );
        }
    }

    async function split_recurring_todo(
        todo: Todo,
        rrule: string,
        attributes: TodoDraft
    ): Promise<void> {
        if (
            draft.rrule !== null &&
            isDeepEqual(RRule.parseString(rrule), RRule.parseString(draft.rrule))
        ) {
            return;
        }

        const split_date = toCalendarDate(toTimeZone(draft.scheduled_at, UTC));
        attributes.rrule = new RRule({
            ...RRule.parseString(rrule),
            count: null,
            until: toZoned(split_date, UTC).subtract({ seconds: 1 }).toDate()
        }).toString();

        const new_id = await TodoRepository.create({ ...todo, ...draft });

        if (draft.rrule === null) {
            await RecurrenceExceptionRepository.destroy("todo", id, split_date);
        } else {
            await RecurrenceExceptionRepository.transfer("todo", id, new_id, split_date);
        }

        await TodoPositionRepository.transfer(id, new_id, split_date);
        await reset_positions({ ...todo, ...draft, id: new_id });
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
    async function destroy_following(todo: Todo, rrule: string) {
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
                await destroy_following(todo, todo.rrule);
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
