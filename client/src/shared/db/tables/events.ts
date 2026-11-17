export type Event = {
    id: string;

    title: string;
    description: string;
    location: string | null;
    color: string | null;

    rrule: string | null;
    recurring: 0 | 1;

    starts_at: number;
    ends_at: number;
    created_at: number;

    notify_before: string;

    timezone: string;
};
