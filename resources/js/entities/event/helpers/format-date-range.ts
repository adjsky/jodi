import { DateFormatter, toCalendarDate } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";
import { TIMEZONE } from "$/shared/cfg/constants";

import type { ZonedDateTime } from "@internationalized/date";

export function formatDateRange(
    startsAt: ZonedDateTime,
    endsAt: ZonedDateTime
) {
    const f = new DateFormatter(getLocale(), {
        day: "2-digit",
        month: "short",
        weekday: "short"
    });
    const yearf = new DateFormatter(getLocale(), {
        day: "2-digit",
        year: "numeric",
        month: "short",
        weekday: "short"
    });

    const s = toCalendarDate(startsAt);
    const e = toCalendarDate(endsAt);

    if (s.compare(e) == 0) {
        return yearf.format(s.toDate(TIMEZONE));
    }

    return `${f.format(s.toDate(TIMEZONE))} - ${f.format(e.toDate(TIMEZONE))}`;
}
