import type { ZonedDateTime } from "@internationalized/date";
import type { WithRecurrence } from "#entities/recurrence/mod.ts";

export type Category = {
    id: string;
    name: string;
};

export type Todo = {
    id: string;

    title: string;
    description: string;

    category_id: string | null;
    color: string | null;

    rrule: string | null;

    scheduled_at: ZonedDateTime;
    has_time: boolean;
    completed_at: ZonedDateTime | null;
    created_at: ZonedDateTime;

    notify_before: string;

    timezone: string;
};

export type TodoDraft = {
    title: string;
    description: string;

    category_id: string | null;
    color: string | null;

    rrule: string | null;

    completed_at?: ZonedDateTime | null;
    scheduled_at: ZonedDateTime;
    has_time: boolean;

    notify_before: string;
};

export type TodoOccurrence = WithRecurrence<Todo>;
