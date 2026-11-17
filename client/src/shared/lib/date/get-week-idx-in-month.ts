import { getDayOfWeek, startOfMonth } from "@internationalized/date";
import { WEEK_START_MAP } from "#shared/cfg/constants.ts";

import type { WeekStart } from "./types";
import type { DateValue } from "@internationalized/date";
import type { Locale } from "#paraglide/runtime.js";

export function get_week_idx_in_month(
    date: DateValue,
    locale: Locale,
    week_start: WeekStart
): number {
    const month_start = startOfMonth(date);
    const offset = getDayOfWeek(month_start, locale, WEEK_START_MAP[week_start]);

    return Math.floor((offset + date.day - 1) / 7);
}
