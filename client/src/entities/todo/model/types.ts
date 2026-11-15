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

    categoryId: string | null;
    color: string | null;

    rrule: string | null;

    scheduledAt: ZonedDateTime;
    hasTime: boolean;
    completedAt: ZonedDateTime | null;
    createdAt: ZonedDateTime;

    notifyBefore: string;

    timezone: string;
};

export type TodoDraft = {
    title: string;
    description: string;

    categoryId: string | null;
    color: string | null;

    rrule: string | null;

    scheduledAt: ZonedDateTime;
    hasTime: boolean;

    notifyBefore: string;
};

export type TodoOccurrence = WithRecurrence<Todo>;
