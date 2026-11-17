import type { TodoOccurrence } from "#entities/todo/mod.ts";

export function id(todo: TodoOccurrence): string {
    return todo.id + "|" + (todo.occurs_at ?? todo.scheduled_at);
}
