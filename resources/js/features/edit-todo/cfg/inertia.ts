import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/inertia/visit/optimistic";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["todos"],
    preserveState: true,
    preserveScroll: true,
    replace: true
};

export const optimisticEdit = (id: number) =>
    optimistic(
        (prev, data) => ({
            todos: prev.todos.map((t: App.Data.TodoDto) =>
                t.id == id
                    ? {
                          ...t,
                          ...data
                      }
                    : t
            )
        }),
        {
            error: m["todos.errors.edit"](),
            preserveUrl: "without-hash"
        }
    );
