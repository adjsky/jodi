import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["events"],
    preserveState: true,
    preserveScroll: true,
    replace: true
};

export const optimistic = {
    edit: (id: number) =>
        _optimistic(
            (prev, data) => ({
                todos: prev.todos.map((t: App.Data.EventDto) =>
                    t.id == id ? { ...t, ...data } : t
                )
            }),
            {
                error: m["events.errors.edit"](),
                isHistoryView: true,
                omitHash: true
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                todos: prev.events.filter((t: App.Data.EventDto) => t.id != id)
            }),
            {
                error: m["events.errors.delete"](),
                isHistoryView: true,
                omitHash: true
            }
        )
};
