import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { TodoData } from "$/entities/todo";

export const destroy = (todo: TodoData) =>
    optimistic<{ todos: TodoData[] }>(
        (prev) => ({
            todos: prev.todos.filter((t) => id(t) != id(todo))
        }),
        {
            rollbackError: m["todos.errors.destroy"](),
            onStart() {
                void editView.back();
            }
        }
    );
