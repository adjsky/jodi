import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";

import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["events"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (id: number, close = true) =>
        _optimistic(
            (prev, data) => ({
                events: prev.events.map((t: App.Data.EventDto) =>
                    t.id == id ? { ...t, ...data } : t
                )
            }),
            {
                error: m["events.errors.edit"](),
                ...(close && { onSuccess: () => editView.back() })
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                events: prev.events.filter((t: App.Data.EventDto) => t.id != id)
            }),
            {
                error: m["events.errors.delete"](),
                onSuccess: () => editView.back()
            }
        )
};
