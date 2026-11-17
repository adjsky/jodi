import type { OrderedWeekday } from "../model/types";
import type { ZonedDateTime } from "@internationalized/date";

export function date_to_weekday(date: Date): number {
    return (date.getDay() + 6) % 7;
}

export function get_ordered_weekdays(start: ZonedDateTime): OrderedWeekday[] {
    return Array.from({ length: 7 }).map((_, idx) => {
        const date = start.add({ days: idx }).toDate();
        return {
            date,
            weekday: date_to_weekday(date)
        };
    });
}
