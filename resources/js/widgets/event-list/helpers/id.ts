import type { EventData } from "$/entities/event/model/types";

export function id(event: EventData) {
    return event.id + "|" + (event.occursAt ?? event.startsAt);
}
