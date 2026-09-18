import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";
import { Push } from "$/shared/services/push";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { TodoData } from "$/entities/todo";
import type { InertiaFormData } from "$/shared/integrations/inertia";

export const edit = (todo: TodoData, hasNotifyAt: boolean) =>
    optimistic<{ todos: TodoData[] }, InertiaFormData>(
        (prev, data) => ({
            todos: prev.todos.map((t) =>
                id(t) == id(todo) ? { ...t, ...data } : t
            )
        }),
        {
            rollbackError: m["todos.errors.edit"](),
            onStart() {
                if (hasNotifyAt) {
                    Push.subscription.ahtung(m["todos.reminder-ahtung"]());
                }
                void editView.back();
            }
        }
    );
