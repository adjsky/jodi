import { parseDuration } from "@internationalized/date";

import type { ZonedDateTime } from "@internationalized/date";

export function durationToZonedDT(startsAt: ZonedDateTime, duration: string) {
    return startsAt.subtract(parseDuration(duration));
}
