import type { CalendarDate } from "@internationalized/date";

export function getWeekDays(start: CalendarDate): CalendarDate[] {
    return Array.from({ length: 7 }).map((_, i) => start.add({ days: i }));
}
