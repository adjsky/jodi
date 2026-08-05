import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";
import { Push } from "$/shared/services/push";
import { toaster } from "$/shared/ui/toaster";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { VisitCallbacks } from "@inertiajs/core";
import type { ZonedDateTime } from "@internationalized/date";
import type { EventData } from "$/entities/event";

export function edit(
    event: EventData,
    draft: { startsAt: ZonedDateTime; endsAt: ZonedDateTime }
): Partial<VisitCallbacks> {
    return optimistic(
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
                Push.subscription.ahtung(m["events.reminder-ahtung"]());
                void editView.back();
            }
        }
    );
}
