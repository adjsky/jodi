import type { CalendarDate } from "@internationalized/date";
import type { CalendarRange } from "@lucide/svelte";

export type RecurrenceScope = "this" | "following" | "all";

export type WithRecurrence<T> = T & {
    occursAt: CalendarDate;
    recurringSince: CalendarRange;
};
