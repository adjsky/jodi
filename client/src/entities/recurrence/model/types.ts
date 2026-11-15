import type { CalendarDate } from "@internationalized/date";

export type RecurrenceScope = "this" | "following" | "all";

export type WithRecurrence<T> = T & {
    occursAt: CalendarDate;
    recurringSince: CalendarDate;
};
