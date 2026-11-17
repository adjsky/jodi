import { toCalendarDate } from "@internationalized/date";

import type { DayPosition } from "../model/types";
import type { CalendarDate, ZonedDateTime } from "@internationalized/date";

export function get_event_day_position(
    starts_at: ZonedDateTime,
    ends_at: ZonedDateTime,
    current_date: CalendarDate
): DayPosition {
    const start_date = toCalendarDate(starts_at);
    const end_date = toCalendarDate(ends_at.subtract({ milliseconds: 1 }));

    return {
        day: days_between(start_date, current_date) + 1,
        total: days_between(start_date, end_date) + 1
    };
}

function days_between(start: CalendarDate, end: CalendarDate) {
    return end.calendar.toJulianDay(end) - start.calendar.toJulianDay(start);
}
