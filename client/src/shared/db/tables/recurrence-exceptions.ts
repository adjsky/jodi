import type { Event } from "./events.ts";
import type { Todo } from "./todos.ts";

export type RecurrenceException = {
    id: string;
    recurrenceableId: string;
    occursAt: string;
    isCancelled: 0 | 1;
} & (
    | {
          recurrenceableType: "todo";
          overrides: Partial<
              Pick<
                  Todo,
                  | "title"
                  | "description"
                  | "categoryId"
                  | "color"
                  | "scheduledAt"
                  | "hasTime"
                  | "completedAt"
                  | "notifyBefore"
              >
          >;
      }
    | {
          recurrenceableType: "event";
          overrides: Partial<
              Pick<
                  Event,
                  | "title"
                  | "description"
                  | "location"
                  | "color"
                  | "startsAt"
                  | "endsAt"
                  | "notifyBefore"
              >
          >;
      }
);
