import { router } from "@inertiajs/core";
import { daySummary } from "$/features/filter-by-date";
import { m } from "$/paraglide/messages";
import { WEEK_CAROUSEL_CACHE_TAG } from "$/shared/cfg/constants";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";
import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
import { toaster } from "$/shared/lib/toaster";

import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";
import type { ZonedDateTime } from "@internationalized/date";

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
        draft: { startsAt: ZonedDateTime; endsAt: ZonedDateTime }
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
                    if (draft.startsAt.compare(draft.endsAt) >= 0) {
                        toaster.error(m["common.invalid-time-range"]());
                        return false;
                    }
                },
                onSuccess: () => {
                    PushSubscription.ahtung(m["events.reminder-ahtung"]());

                    router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);
                    daySummary.flush();

                    void editView.back();
                }
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                events: prev.events.filter((e: App.Data.EventDto) => e.id != id)
            }),
            {
                error: m["events.errors.delete"](),
                onSuccess: () => {
                    router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);
                    daySummary.flush();

                    void editView.back();
                }
            }
        )
};
