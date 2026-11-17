import { fromAbsolute } from "@internationalized/date";
import { TIMEZONE } from "#shared/cfg/constants.ts";
import { fromDbBoolean, toDbBoolean } from "#shared/db/boolean.ts";
import { db } from "#shared/db/dexie.ts";
import Dexie from "dexie";
import { omitBy } from "remeda";

import type { Todo, TodoDraft } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";

function list(date: CalendarDate): Promise<Todo[]> {
    const start = date.toDate(TIMEZONE).getTime();
    const end = date.add({ days: 1 }).toDate(TIMEZONE).getTime();

    return db.todos
        .where("[recurring+scheduledAt]")
        .between([0, start], [0, end], true, false)
        .or("[recurring+scheduledAt]")
        .between([1, Dexie.minKey], [1, end], true, false)
        .toArray((todos) =>
            todos.map((t) => ({
                ...t,
                scheduledAt: fromAbsolute(t.scheduledAt, t.timezone),
                hasTime: fromDbBoolean(t.hasTime),
                completedAt:
                    t.completedAt !== null
                        ? fromAbsolute(t.completedAt, t.timezone)
                        : null,
                createdAt: fromAbsolute(t.createdAt, TIMEZONE)
            }))
        );
}

function create(draft: TodoDraft): Promise<string> {
    return db.todos.add({
        id: crypto.randomUUID(),
        title: draft.title,
        description: draft.description,
        categoryId: draft.categoryId,
        color: draft.color,
        rrule: draft.rrule,
        recurring: toDbBoolean(draft.rrule !== null),
        scheduledAt: draft.scheduledAt.toDate().getTime(),
        hasTime: toDbBoolean(draft.hasTime),
        completedAt: null,
        createdAt: Date.now(),
        notifyBefore: draft.notifyBefore,
        timezone: TIMEZONE
    });
}

function update(id: string, draft: Partial<TodoDraft>): Promise<number> {
    return db.todos.update(
        id,
        omitBy(
            {
                title: draft.title,
                description: draft.description,
                categoryId: draft.categoryId,
                color: draft.color,
                rrule: draft.rrule,
                recurring:
                    draft.rrule !== undefined
                        ? toDbBoolean(draft.rrule !== null)
                        : undefined,
                scheduledAt: draft.scheduledAt?.toDate().getTime(),
                hasTime: draft.hasTime ? toDbBoolean(draft.hasTime) : undefined,
                notifyBefore: draft.notifyBefore
            },
            (v) => v === undefined
        )
    );
}

async function complete(id: string): Promise<void> {
    await db.todos.update(id, (todo) => {
        todo.completedAt = todo.completedAt ? null : Date.now();
    });
}

function destroy(id: string): Promise<void> {
    return db.todos.delete(id);
}

export const TodoRepository = {
    list,
    create,
    update,
    complete,
    destroy
};
