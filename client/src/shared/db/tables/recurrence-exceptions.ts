export type RecurrenceException = {
    id: string;

    recurrenceableType: "todo" | "event";
    recurrenceableId: string;

    occursAt: string;
    isCancelled: 0 | 1;
    overrides: Record<string, unknown>;
};
