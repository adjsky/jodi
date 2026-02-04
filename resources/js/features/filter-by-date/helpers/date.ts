import { getDayOfWeek, isEqualDay } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";

import type { CalendarDate } from "@internationalized/date";

export function getWeekDays(start: CalendarDate) {
    return Array.from({ length: 7 }).map((_, i) => start.add({ days: i }));
}

export function compareDates(a: CalendarDate, b: CalendarDate) {
    if (isEqualDay(a, b)) {
        return "selected";
    }

    const locale = getLocale();

    if (getDayOfWeek(a, locale) == getDayOfWeek(b, locale)) {
        return "ghost";
    }

    return false;
}
