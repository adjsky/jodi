import { now, toCalendarDateTime, toZoned } from "@internationalized/date";

import { TIMEZONE } from "../../cfg/constants";

import type { CalendarDate } from "@internationalized/date";

type WithCurrentTimeOptions = {
    hourOffset?: number;
    minuteOffset?: number;
};

export function withCurrentTime(
    date: CalendarDate,
    options?: WithCurrentTimeOptions
) {
    const { hourOffset, minuteOffset } = options ?? {};

    const datetime = toZoned(toCalendarDateTime(date, now(TIMEZONE)), TIMEZONE);

    if (hourOffset || minuteOffset) {
        return datetime
            .set({ minute: 0, second: 0, millisecond: 0 })
            .add({ hours: hourOffset, minutes: minuteOffset });
    }

    return datetime;
}
