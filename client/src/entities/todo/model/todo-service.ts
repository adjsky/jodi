import { db } from "#shared/db/dexie.ts";
import { liveQuery } from "#shared/db/live-query.ts";
import { sortBy } from "remeda";

import { CategoryRepository, TodoPositionRepository } from "../mod.ts";
import { TodoRepository } from "./todo-repository.ts";

import type { Todo } from "../mod.ts";
import type { TodoDraft } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";
import type { LiveQuery } from "#shared/db/live-query.ts";

function list(date: CalendarDate): LiveQuery<Todo[]> {
    return liveQuery(async () => {
        const [todos, positions, categories] = await db.transaction(
            "r",
            [db.todos, db.todoPositions, db.categories],
            () =>
                Promise.all([
                    TodoRepository.list(date),
                    TodoPositionRepository.list(date),
                    CategoryRepository.list()
                ])
        );

        const category = new Map(categories.map((c) => [c.id, c.name]));
        const position = new Map(positions.map((p) => [p.todoId, p.position]));

        return sortBy(
            todos,
            (t) => category.get(t.id) ?? "",
            (t) => position.get(t.id) ?? Infinity,
            (t) => t.createdAt.toDate().getTime()
        );
    });
}

async function create(draft: TodoDraft): Promise<string> {
    const id = await TodoRepository.create(draft);

    return id;
}

export const TodoService = {
    list,
    create
};
