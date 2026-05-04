import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";
import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
import { toaster } from "$/shared/lib/toaster";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";
import type { ZonedDateTime } from "@internationalized/date";
import type { EventData } from "$/entities/event/model/types";

export const visitOptions: VisitOptions = {
    only: ["events"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (
        event: EventData,
        draft: { startsAt: ZonedDateTime; endsAt: ZonedDateTime }
    ) =>
        _optimistic(
            (prev, data) => ({
                events: prev.events.map((e: EventData) =>
                    id(e) === id(event) ? { ...e, ...data } : e
                )
            }),
            {
                error: m["events.errors.edit"](),
                onBefore() {
                    if (draft.startsAt.compare(draft.endsAt) >= 0) {
                        toaster.error(m["common.invalid-time-range"]());
                        return false;
                    }
                },
                onSuccess() {
                    PushSubscription.ahtung(m["events.reminder-ahtung"]());
                    void editView.back();
                }
            }
        ),
    delete: (event: EventData) =>
        _optimistic(
            (prev) => ({
                events: prev.events.filter(
                    (e: EventData) => id(e) !== id(event)
                )
            }),
            {
                error: m["events.errors.delete"](),
                onSuccess() {
                    void editView.back();
                }
            }
        )
};
