import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";

import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["todos", "categories"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (id: number, close = true) =>
        _optimistic(
            (prev, data) => ({
                todos: prev.todos.map((t: App.Data.TodoDto) =>
                    t.id == id ? { ...t, ...data } : t
                )
            }),
            {
                error: m["todos.errors.edit"](),
                onSuccess(props) {
                    const todos = props.todos as App.Data.TodoDto[];

                    if (close) {
                        return editView.back();
                    }

                    const todo = todos.find((t) => t.id == id);
                    if (!todo) return;

                    return editView.updateMeta(todo);
                }
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                todos: prev.todos.filter((t: App.Data.TodoDto) => t.id != id)
            }),
            {
                error: m["todos.errors.delete"](),
                onSuccess: () => editView.back()
            }
        )
};
