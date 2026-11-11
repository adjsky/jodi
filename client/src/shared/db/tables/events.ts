export type Event = {
    id: string;

    title: string;
    description: string;
    location: string | null;
    color: string | null;

    rrule: string | null;
    recurring: 0 | 1;

    startsAt: number;
    endsAt: number;
    createdAt: number;

    notifyBefore: string;

    timezone: string;
};
