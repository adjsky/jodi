import type { Event } from "./events.ts";
import type { Todo } from "./todos.ts";

export type RecurrenceException = {
    id: string;
    recurrenceable_id: string;
    occurs_at: string;
    is_cancelled: 0 | 1;
} & (
    | {
          recurrenceable_type: "todo";
          overrides: Partial<
              Pick<
                  Todo,
                  | "title"
                  | "description"
                  | "category_id"
                  | "color"
                  | "scheduled_at"
                  | "has_time"
                  | "completed_at"
                  | "notify_before"
              >
          >;
      }
    | {
          recurrenceable_type: "event";
          overrides: Partial<
              Pick<
                  Event,
                  | "title"
                  | "description"
                  | "location"
                  | "color"
                  | "starts_at"
                  | "ends_at"
                  | "notify_before"
              >
          >;
      }
);
