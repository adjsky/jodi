import {
    DateFormatter,
    now,
    toCalendarDateTime,
    toZoned
} from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";

import { TIMEZONE } from "../cfg/constants";

import type { CalendarDate, TimeDuration } from "@internationalized/date";

export function formatToHHMM(date: Date) {
    return new DateFormatter(getLocale(), {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false
    }).format(date);
}

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

export function normalizeIsoString(iso8601: string) {
    const [date, tzTime] = iso8601.split("T");
    const [time, _] = tzTime.split(".");

    return `${date}T${time}+00:00`;
}

type TimeLike = {
    hour: number;
    minute: number;
    second: number;
    millisecond: number;
};

export function diff(a: TimeLike, b: TimeLike): TimeDuration {
    return {
        hours: (b.hour ?? 0) - (a.hour ?? 0),
        minutes: (b.minute ?? 0) - (a.minute ?? 0),
        seconds: (b.second ?? 0) - (a.second ?? 0),
        milliseconds: (b.millisecond ?? 0) - (a.millisecond ?? 0)
    };
}
