import { db } from "#shared/db/dexie.ts";

import type { CalendarDate } from "@internationalized/date";
import type { TodoPosition } from "#shared/db/tables/todo-positions.ts";

function list(date: CalendarDate): Promise<TodoPosition[]> {
    return db.todoPositions.where({ date: date.toString() }).toArray();
}

export const TodoPositionRepository = {
    list
};
