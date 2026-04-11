import { UNGROUPED_KEY } from "../cfg/constants";

import type { TodoData } from "$/entities/todo";

export function groupTodos(todos: TodoData[]) {
    const groups: Record<string, TodoData[]> = {};

    for (const todo of todos) {
        const group = todo.category?.name || UNGROUPED_KEY;

        if (!groups[group]) {
            groups[group] = [];
        }

        groups[group].push(todo);
    }

    return groups;
}
