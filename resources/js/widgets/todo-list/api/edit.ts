import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";
import { Push } from "$/shared/services/push";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { VisitCallbacks } from "@inertiajs/core";
import type { TodoData } from "$/entities/todo";

export function edit(
    todo: TodoData,
    withAhtungReminder: boolean
): Partial<VisitCallbacks> {
    return optimistic(
        (prev, data) => ({
            todos: prev.todos.map((t: TodoData) =>
                id(t) === id(todo) ? { ...t, ...data } : t
            )
        }),
        {
            error: m["todos.errors.edit"](),
            onSuccess() {
                if (withAhtungReminder) {
                    Push.subscription.ahtung(m["todos.reminder-ahtung"]());
                }
                void editView.back();
            }
        }
    );
}
