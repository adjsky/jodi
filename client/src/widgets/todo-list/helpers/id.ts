import type { TodoData } from "$/entities/todo";

export function id(todo: TodoData): string {
    return todo.id + "|" + (todo.occursAt ?? todo.scheduledAt);
}
