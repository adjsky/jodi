import type { OrderedWeekday } from "../model/types";
import type { ZonedDateTime } from "@internationalized/date";

export function dateToWeekday(date: Date): number {
    return (date.getDay() + 6) % 7;
}

export function getOrderedWeekdays(start: ZonedDateTime): OrderedWeekday[] {
    return Array.from({ length: 7 }).map((_, idx) => {
        const date = start.add({ days: idx }).toDate();
        return {
            date,
            weekday: dateToWeekday(date)
        };
    });
}
