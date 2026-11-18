import { fromAbsolute } from "@internationalized/date";
import { TIMEZONE } from "#shared/cfg/constants.ts";
import { from_db_boolean, to_db_boolean } from "#shared/db/boolean.ts";
import { db } from "#shared/db/dexie.ts";
import Dexie from "dexie";
import { omitBy } from "remeda";

import type { Todo, TodoDraft } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";
import type { Todo as DatabaseTodo } from "#shared/db/tables/todos.ts";

function map(todo: DatabaseTodo): Todo {
    return {
        ...todo,
        scheduled_at: fromAbsolute(todo.scheduled_at, todo.timezone),
        has_time: from_db_boolean(todo.has_time),
        completed_at:
            todo.completed_at !== null ? fromAbsolute(todo.completed_at, todo.timezone) : null,
        created_at: fromAbsolute(todo.created_at, TIMEZONE),
        notify_before: todo.notify_before
    };
}

async function find(id: string): Promise<Todo | null> {
    const todo = await db.todos.get(id);

    if (!todo) {
        return null;
    }

    return map(todo);
}

function list(date: CalendarDate): Promise<Todo[]> {
    const start = date.toDate(TIMEZONE).getTime();
    const end = date.add({ days: 1 }).toDate(TIMEZONE).getTime();

    return db.todos
        .where("[recurring+scheduled_at]")
        .between([0, start], [0, end], true, false)
        .or("[recurring+scheduled_at]")
        .between([1, Dexie.minKey], [1, end], true, false)
        .toArray((todos) => todos.map(map));
}

function create(draft: TodoDraft): Promise<string> {
    return db.todos.add({
        ...draft,
        id: crypto.randomUUID(),
        recurring: to_db_boolean(draft.rrule !== null),
        scheduled_at: draft.scheduled_at.toDate().getTime(),
        has_time: to_db_boolean(draft.has_time),
        completed_at: draft.completed_at?.toDate().getTime() ?? null,
        created_at: Date.now(),
        timezone: TIMEZONE
    });
}

function replicate(todo: Todo, overrides: Partial<TodoDraft> = {}): Promise<string> {
    const replica = { ...todo, ...overrides };

    return db.todos.add({
        ...replica,
        id: crypto.randomUUID(),
        recurring: to_db_boolean(replica.rrule !== null),
        scheduled_at: replica.scheduled_at.toDate().getTime(),
        has_time: to_db_boolean(replica.has_time),
        completed_at: replica.completed_at?.toDate().getTime() ?? null,
        created_at: replica.created_at.toDate().getTime()
    });
}

function update(id: string, draft: Partial<TodoDraft>): Promise<number> {
    return db.todos.update(
        id,
        omitBy(
            {
                title: draft.title,
                description: draft.description,
                category_id: draft.category_id,
                color: draft.color,
                rrule: draft.rrule,
                recurring:
                    draft.rrule !== undefined ? to_db_boolean(draft.rrule !== null) : undefined,
                scheduled_at: draft.scheduled_at?.toDate().getTime(),
                has_time: draft.has_time !== undefined ? to_db_boolean(draft.has_time) : undefined,
                completed_at:
                    draft.completed_at === null ? null : draft.completed_at?.toDate().getTime(),
                notify_before: draft.notify_before
            },
            (v) => v === undefined
        )
    );
}

function destroy(id: string): Promise<void> {
    return db.todos.delete(id);
}

export const TodoRepository = {
    find,
    list,
    create,
    replicate,
    update,
    destroy
};
