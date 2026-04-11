import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";
import { normalizeIsoString } from "$/shared/lib/date";
import * as PushSubscription from "$/shared/lib/push-subscription.svelte";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";
import type { TodoData } from "$/entities/todo";

export const visitOptions: VisitOptions = {
    only: ["todos", "categories"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (todo: TodoData, withAhtungReminder: boolean) =>
        _optimistic(
            (prev, data) => ({
                todos: prev.todos.map((t: TodoData) =>
                    id(t) === id(todo) ? { ...t, ...data } : t
                )
            }),
            {
                error: m["todos.errors.edit"](),
                onSuccess() {
                    if (withAhtungReminder) {
                        PushSubscription.ahtung(m["todos.reminder-ahtung"]());
                    }
                    void editView.back();
                }
            }
        ),
    complete: (todo: TodoData) =>
        _optimistic(
            (prev) => ({
                todos: prev.todos.map((t: TodoData) =>
                    id(t) === id(todo)
                        ? {
                              ...t,
                              completedAt: t.completedAt
                                  ? null
                                  : normalizeIsoString(new Date().toISOString())
                          }
                        : t
                )
            }),
            {
                error: m["todos.errors.complete"](),
                onSuccess(props) {
                    const todos = props.todos as TodoData[];

                    const updatedTodo = todos.find((t) => id(t) === id(todo));
                    if (!updatedTodo || !editView.isOpen()) return;

                    return editView.updateMeta(updatedTodo);
                }
            }
        ),
    delete: (todo: TodoData) =>
        _optimistic(
            (prev) => ({
                todos: prev.todos.filter((t: TodoData) => id(t) !== id(todo))
            }),
            {
                error: m["todos.errors.delete"](),
                onSuccess() {
                    void editView.back();
                }
            }
        )
};
