import type { CalendarDate, ZonedDateTime } from "@internationalized/date";

export type RecurrenceScope = "this" | "following" | "all";

export type RecurrenceException = {
    id: string;
    recurrenceable_id: string;
    occurs_at: string;
    is_cancelled: boolean;
} & (
    | {
          recurrenceable_type: "todo";
          overrides: Partial<{
              title: string;
              description: string;
              category_id: string | null;
              color: string | null;
              scheduled_at: ZonedDateTime;
              has_time: boolean;
              completed_at: ZonedDateTime | null;
              notify_before: string;
          }>;
      }
    | {
          recurrenceable_type: "event";
          overrides: Partial<{
              title: string;
              description: string;
              location: string | null;
              color: string | null;
              starts_at: ZonedDateTime;
              ends_at: ZonedDateTime;
              notify_before: string;
          }>;
      }
);

export type WithRecurrence<T> = T & {
    occurs_at: CalendarDate | null;
    recurring_since: CalendarDate | null;
};
