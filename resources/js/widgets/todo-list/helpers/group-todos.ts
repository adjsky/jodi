import { UNGROUPED_KEY } from "../cfg/constants";

export function groupTodos(todos: App.Data.TodoDto[]) {
    const groups: Record<string, App.Data.TodoDto[]> = {};

    for (const todo of todos) {
        const group = todo.category || UNGROUPED_KEY;

        if (!groups[group]) {
            groups[group] = [];
        }

        groups[group].push(todo);
    }

    return groups;
}
