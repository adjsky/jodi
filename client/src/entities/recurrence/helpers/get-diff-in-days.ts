import type { ZonedDateTime } from "@internationalized/date";

export function getDiffInDays(
    later: ZonedDateTime,
    earlier: ZonedDateTime
): number {
    return (
        later.calendar.toJulianDay(later) -
        earlier.calendar.toJulianDay(earlier)
    );
}
