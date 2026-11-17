import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";
import { Push } from "$/shared/services/push";
import { toaster } from "$/shared/ui/toaster";

import { id } from "../helpers/id";
import { edit_view } from "../model/view";

import type { ZonedDateTime } from "@internationalized/date";
import type { EventData } from "$/entities/event";
import type { InertiaFormData } from "$/shared/integrations/inertia";

export const edit = (
    event: EventData,
    draft: { starts_at: ZonedDateTime; ends_at: ZonedDateTime }
) =>
    optimistic<{ events: EventData[] }, InertiaFormData>(
        (prev, data) => ({
            events: prev.events.map((e) => (id(e) === id(event) ? { ...e, ...data } : e))
        }),
        {
            rollbackError: m["events.errors.edit"](),
            onBefore() {
                if (draft.starts_at.compare(draft.ends_at) >= 0) {
                    toaster.error(m["common.invalid-time-range"]());
                    return false;
                }
            },
            onStart() {
                Push.subscription.ahtung(m["events.reminder-ahtung"]());
                void edit_view.back();
            }
        }
    );
