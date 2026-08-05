import { getDayOfWeek, startOfMonth } from "@internationalized/date";
import { WEEK_START_MAP } from "$/shared/cfg/constants";

import type { WeekStart } from "./types";
import type { DateValue } from "@internationalized/date";
import type { Locale } from "$/paraglide/runtime";

export function getWeekIdxInMonth(
    date: DateValue,
    locale: Locale,
    weekStart: WeekStart
): number {
    const monthStart = startOfMonth(date);
    const offset = getDayOfWeek(monthStart, locale, WEEK_START_MAP[weekStart]);

    return Math.floor((offset + date.day - 1) / 7);
}
