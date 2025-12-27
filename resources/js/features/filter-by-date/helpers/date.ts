import { getDayOfWeek, isEqualDay } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";

import type { DateValue } from "@internationalized/date";

export function getWeekDays(start: DateValue) {
    return Array.from({ length: 7 }).map((_, i) => start.add({ days: i }));
}

export function compareDates(a: DateValue, b: DateValue) {
    if (isEqualDay(a, b)) {
        return "selected";
    }

    const locale = getLocale();

    if (getDayOfWeek(a, locale) == getDayOfWeek(b, locale)) {
        return "ghost";
    }

    return false;
}
