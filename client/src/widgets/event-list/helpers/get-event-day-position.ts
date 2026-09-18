import { toCalendarDate } from "@internationalized/date";

import type { DayPosition } from "../model/types";
import type { CalendarDate, ZonedDateTime } from "@internationalized/date";

export function getEventDayPosition(
    startsAt: ZonedDateTime,
    endsAt: ZonedDateTime,
    currentDate: CalendarDate
): DayPosition {
    const startDate = toCalendarDate(startsAt);
    const endDate = toCalendarDate(endsAt.subtract({ milliseconds: 1 }));

    return {
        day: daysBetween(startDate, currentDate) + 1,
        total: daysBetween(startDate, endDate) + 1
    };
}

function daysBetween(start: CalendarDate, end: CalendarDate) {
    return end.calendar.toJulianDay(end) - start.calendar.toJulianDay(start);
}
