import type { ZonedDateTime } from "@internationalized/date";

export function dateToWeekday(date: Date) {
    return (date.getDay() + 6) % 7;
}

export function getOrderedWeekdays(start: ZonedDateTime) {
    return Array.from({ length: 7 }).map((_, idx) => {
        const date = start.add({ days: idx }).toDate();
        return {
            date,
            weekday: dateToWeekday(date)
        };
    });
}
