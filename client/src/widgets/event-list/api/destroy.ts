import { m } from "$/paraglide/messages";
import { optimistic } from "$/shared/integrations/inertia";

import { id } from "../helpers/id";
import { editView } from "../model/view";

import type { EventData } from "$/entities/event";

export const destroy = (event: EventData) =>
    optimistic<{ events: EventData[] }>(
        (prev) => ({
            events: prev.events.filter((e) => id(e) != id(event))
        }),
        {
            rollbackError: m["events.errors.destroy"](),
            onStart() {
                void editView.back();
            }
        }
    );
