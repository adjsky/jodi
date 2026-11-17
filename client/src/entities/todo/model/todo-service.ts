import { now } from "@internationalized/date";
import {
    RecurrenceExceptionRepository,
    RecurrenceExpander
} from "#entities/recurrence/mod.ts";
import { TIMEZONE } from "#shared/cfg/constants.ts";
import { db } from "#shared/db/dexie.ts";
import { liveQuery } from "#shared/db/live-query.svelte.ts";
import { sortBy } from "remeda";

import { CategoryRepository, TodoPositionRepository } from "../mod.ts";
import { TodoRepository } from "./todo-repository.ts";

import type { TodoDraft, TodoOccurrence } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";
import type { LiveQuery } from "#shared/db/live-query.svelte.ts";
import type { MaybeGetter } from "runed";

function list(date: MaybeGetter<CalendarDate>): LiveQuery<TodoOccurrence[]> {
    return liveQuery(date, async (date) => {
        const [todos, exceptions, positions, categories] = await db.transaction(
            "r",
            [
                db.todos,
                db.recurrenceExceptions,
                db.todoPositions,
                db.categories
            ],
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
        const position = new Map(positions.map((p) => [p.todoId, p.position]));

        const expander = new RecurrenceExpander({
            startKey: "scheduledAt",
            endKey: null,
            shiftedDateKeys: [],
            timezone: TIMEZONE
        });

        return sortBy(
            expander.on(date, todos, exceptions),
            (t) => (t.categoryId ? (category.get(t.categoryId) ?? "") : ""),
            (t) => position.get(t.id) ?? Infinity,
            (t) => t.createdAt.toDate().getTime()
        );
    });
}

async function create(draft: TodoDraft): Promise<string> {
    const id = await TodoRepository.create(draft);

    return id;
}

async function complete(
    id: string,
    occursAt: CalendarDate | null
): Promise<void> {
    if (occursAt) {
    } else {
        await TodoRepository.complete(id);
    }
}

export const TodoService = {
    list,
    create,
    complete
};
