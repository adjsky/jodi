import { router } from "@inertiajs/core";
import { m } from "$/paraglide/messages";
import { WEEK_CAROUSEL_CACHE_TAG } from "$/shared/cfg/constants";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";
import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
import { toaster } from "$/shared/lib/toaster";

import { id } from "../helpers/id";
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
        event: App.Data.EventDto,
        draft: { startsAt: ZonedDateTime; endsAt: ZonedDateTime }
    ) =>
        _optimistic(
            (prev, data) => ({
                events: prev.events.map((e: App.Data.EventDto) =>
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
                onSuccess: () => {
                    PushSubscription.ahtung(m["events.reminder-ahtung"]());
                    router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);

                    void editView.back();
                }
            }
        ),
    delete: (event: App.Data.EventDto) =>
        _optimistic(
            (prev) => ({
                events: prev.events.filter(
                    (e: App.Data.EventDto) => id(e) !== id(event)
                )
            }),
            {
                error: m["events.errors.delete"](),
                onSuccess: () => {
                    router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);
                    void editView.back();
                }
            }
        )
};
