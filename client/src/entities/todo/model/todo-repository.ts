import { TIMEZONE } from "#shared/cfg/constants.ts";
import { toDbBoolean } from "#shared/db/boolean.ts";
import { db } from "#shared/db/dexie.ts";
import { liveQuery } from "#shared/db/live-query.ts";

import type { Todo, TodoDraft } from "./types.ts";
import type { LiveQuery } from "#shared/db/live-query.ts";

function list(): LiveQuery<Todo[]> {
    // return liveQuery(() =>
    //     db.todos.orderBy("createdAt").toArray((todos) =>
    //         todos.map((t) => ({
    //             id: c.id,
    //             name: c.name
    //         }))
    //     )
    // );
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
        createdAt: Date.now(),
        completedAt: null,
        notifyBefore: draft.notifyBefore,
        timezone: TIMEZONE
    });
}

function put(id: string, draft: TodoDraft): Promise<number> {
    return db.todos.update(id, {
        title: draft.title,
        description: draft.description,
        categoryId: draft.categoryId,
        color: draft.color,
        rrule: draft.rrule,
        recurring: toDbBoolean(draft.rrule !== null),
        scheduledAt: draft.scheduledAt.toDate().getTime(),
        hasTime: toDbBoolean(draft.hasTime),
        notifyBefore: draft.notifyBefore
    });
}

function destroy(id: string): Promise<void> {
    return db.todos.delete(id);
}

export const TodoRepository = {
    list,
    create,
    put,
    destroy
};
