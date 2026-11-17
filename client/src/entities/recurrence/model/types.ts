import type { CalendarDate, ZonedDateTime } from "@internationalized/date";

export type RecurrenceScope = "this" | "following" | "all";

export type RecurrenceException = {
    id: string;
    recurrenceableId: string;
    occursAt: string;
    isCancelled: boolean;
} & (
    | {
          recurrenceableType: "todo";
          overrides: Partial<{
              title: string;
              description: string;
              categoryId: string | null;
              color: string | null;
              scheduledAt: ZonedDateTime;
              hasTime: boolean;
              completedAt: ZonedDateTime | null;
              notifyBefore: string;
          }>;
      }
    | {
          recurrenceableType: "event";
          overrides: Partial<{
              title: string;
              description: string;
              location: string | null;
              color: string | null;
              startsAt: ZonedDateTime;
              endsAt: ZonedDateTime;
              notifyBefore: string;
          }>;
      }
);

export type WithRecurrence<T> = T & {
    occursAt: CalendarDate | null;
    recurringSince: CalendarDate | null;
};
