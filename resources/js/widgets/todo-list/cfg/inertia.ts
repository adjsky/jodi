import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["todos", "categories"],
    preserveState: true,
    preserveScroll: true,
    replace: true
};

export const optimistic = {
    edit: (id: number, keepHash = false) =>
        _optimistic(
            (prev, data) => ({
                todos: prev.todos.map((t: App.Data.TodoDto) =>
                    t.id == id ? { ...t, ...data } : t
                )
            }),
            {
                error: m["todos.errors.edit"](),
                isHistoryView: true,
                omitHash: !keepHash
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                todos: prev.todos.filter((t: App.Data.TodoDto) => t.id != id)
            }),
            {
                error: m["todos.errors.delete"](),
                isHistoryView: true,
                omitHash: true
            }
        )
};
