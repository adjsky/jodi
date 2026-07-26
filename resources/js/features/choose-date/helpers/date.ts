import { getDayOfWeek } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";

import type { CalendarDate } from "@internationalized/date";

export function compareDates(a: CalendarDate, b: CalendarDate) {
    if (a.compare(b) == 0) {
        return "selected";
    }

    const locale = getLocale();

    if (getDayOfWeek(a, locale) == getDayOfWeek(b, locale)) {
        return "ghost";
    }

    return null;
}
