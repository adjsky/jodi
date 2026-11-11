import Dexie from "dexie";

import type { Category } from "./tables/categories.ts";
import type { Event } from "./tables/events.ts";
import type { RecurrenceException } from "./tables/recurrence-exceptions.ts";
import type { Session } from "./tables/session.ts";
import type { TodoPosition } from "./tables/todo-positions.ts";
import type { Todo } from "./tables/todos.ts";
import type { EntityTable } from "dexie";

export const db = new Dexie("jodi") as Dexie & {
    sessions: EntityTable<Session, "id">;
    events: EntityTable<Event, "id">;
    todos: EntityTable<Todo, "id">;
    categories: EntityTable<Category, "id">;
    todoPositions: EntityTable<TodoPosition, "id">;
    recurrenceExceptions: EntityTable<RecurrenceException, "id">;
};

db.version(1).stores({
    sessions: "id",
    events: "id, [recurring+startsAt]",
    todos: "id, [recurring+scheduledAt]",
    categories: "id, &name",
    todoPositions: "id, date, &[todoId+date]",
    recurrenceExceptions: "id, &[recurrenceableType+recurrenceableId+occursAt]"
});
