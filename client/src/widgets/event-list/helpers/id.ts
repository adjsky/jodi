import type { EventData } from "$/entities/event";

export function id(event: EventData): string {
    return event.id + "|" + (event.occursAt ?? event.startsAt);
}
