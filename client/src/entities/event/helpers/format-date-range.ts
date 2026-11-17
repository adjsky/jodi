import { DateFormatter, toCalendarDate } from "@internationalized/date";
import { getLocale } from "#paraglide/runtime.js";
import { TIMEZONE } from "#shared/cfg/constants.ts";

import type { ZonedDateTime } from "@internationalized/date";

export function format_date_range(starts_at: ZonedDateTime, ends_at: ZonedDateTime): string {
    const f = new DateFormatter(getLocale(), {
        day: "2-digit",
        month: "short",
        weekday: "short",
        timeZone: TIMEZONE
    });
    const yearf = new DateFormatter(getLocale(), {
        day: "2-digit",
        year: "numeric",
        month: "short",
        weekday: "short",
        timeZone: TIMEZONE
    });

    const s = toCalendarDate(starts_at);
    const e = toCalendarDate(ends_at);

    if (s.compare(e) === 0) {
        return yearf.format(s.toDate(TIMEZONE));
    }

    return `${f.format(s.toDate(TIMEZONE))} - ${f.format(e.toDate(TIMEZONE))}`;
}
