export function id(todo: App.Data.TodoDto) {
    return todo.id + "|" + (todo.occursAt ?? todo.startsAt);
}
