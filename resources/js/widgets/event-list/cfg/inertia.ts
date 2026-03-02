import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";
import { toaster } from "$/shared/lib/toaster";

import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";
import type { ZonedDateTime } from "@internationalized/date";
import type { Getter } from "runed";

export const visitOptions: VisitOptions = {
    only: ["events"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (
        id: number,
        draft: Getter<{ startsAt: ZonedDateTime; endsAt: ZonedDateTime }>
    ) =>
        _optimistic(
            (prev, data) => ({
                events: prev.events.map((e: App.Data.EventDto) =>
                    e.id == id ? { ...e, ...data } : e
                )
            }),
            {
                error: m["events.errors.edit"](),
                onBefore() {
                    if (draft().startsAt.compare(draft().endsAt) >= 0) {
                        toaster.error(m["common.invalid-time-range"]());
                        return false;
                    }
                },
                onSuccess: () => editView.back()
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                events: prev.events.filter((e: App.Data.EventDto) => e.id != id)
            }),
            {
                error: m["events.errors.delete"](),
                onSuccess: () => editView.back()
            }
        )
};
