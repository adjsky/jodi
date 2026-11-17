import type { ZonedDateTime } from "@internationalized/date";

export function get_diff_in_days(later: ZonedDateTime, earlier: ZonedDateTime): number {
    return later.calendar.toJulianDay(later) - earlier.calendar.toJulianDay(earlier);
}
