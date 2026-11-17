import type { TodoOccurrence } from "#entities/todo/mod.ts";

export function id(todo: TodoOccurrence): string {
    return todo.id + "|" + (todo.occursAt ?? todo.scheduledAt);
}
