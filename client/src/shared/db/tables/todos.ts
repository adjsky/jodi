export type Todo = {
    id: string;

    title: string;
    description: string;

    category_id: string | null;
    color: string | null;

    rrule: string | null;
    recurring: 0 | 1;

    scheduled_at: number;
    has_time: 0 | 1;
    completed_at: number | null;
    created_at: number;

    notify_before: string;

    timezone: string;
};
