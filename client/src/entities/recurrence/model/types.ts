import type { CalendarDate } from "@internationalized/date";

export type RecurrenceScope = "this" | "following" | "all";

export type RecurrenceException = {
    id: string;
    recurrenceableId: string;
    occursAt: string;
    isCancelled: boolean;
    overrides: Record<string, unknown>;
};

export type WithRecurrence<T> = T & {
    occursAt: CalendarDate;
    recurringSince: CalendarDate;
};
