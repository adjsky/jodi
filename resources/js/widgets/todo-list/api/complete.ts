import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";
import { normalizeIsoString } from "$/shared/lib/date/normalize-iso-string";

import { id } from "../helpers/id";

import type { TodoData } from "$/entities/todo";

export const complete = () =>
    optimistic<{ todos: TodoData[] }>(
        (prev) => ({
            todos: prev.todos.map((t) =>
                id(t) === id(t)
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
            error: m["todos.errors.complete"]()
        }
    );
