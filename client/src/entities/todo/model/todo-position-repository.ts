import { db } from "#shared/db/dexie.ts";
import Dexie from "dexie";

import type { CalendarDate } from "@internationalized/date";
import type { TodoPosition } from "#shared/db/tables/todo-positions.ts";

function list(date: CalendarDate): Promise<TodoPosition[]> {
    return db.todo_positions.where({ date: date.toString() }).toArray();
}

async function destroy_all(id: string): Promise<void> {
    await db.todo_positions
        .where("[todo_id+date]")
        .between([id, Dexie.minKey], [id, Dexie.maxKey])
        .delete();
}

async function destroy_for_date(id: string, date: CalendarDate): Promise<void> {
    await db.todo_positions.where("[todo_id+date]").equals([id, date.toString()]).delete();
}

async function destroy_from_date(id: string, from: CalendarDate): Promise<void> {
    await db.todo_positions
        .where("[todo_id+date]")
        .between([id, from.toString()], [id, Dexie.maxKey])
        .delete();
}

function transfer(id: string, new_id: string, from: CalendarDate): Promise<number> {
    return db.todo_positions
        .where("[todo_id+date]")
        .between([id, from.toString()], [id, Dexie.maxKey])
        .modify({ todo_id: new_id });
}

export const TodoPositionRepository = {
    list,
    destroy_all,
    destroy_for_date,
    destroy_from_date,
    transfer
};
