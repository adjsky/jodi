import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";
import { normalizeIsoString } from "$/shared/lib/date/normalize-iso-string";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { TodoData } from "$/entities/todo";

export const complete = (todo: TodoData) =>
    optimistic(
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
    );
