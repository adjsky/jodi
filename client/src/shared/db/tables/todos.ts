export type Todo = {
    id: string;

    title: string;
    description: string;

    categoryId: string | null;
    color: string | null;

    rrule: string | null;
    recurring: 0 | 1;

    scheduledAt: number;
    hasTime: 0 | 1;
    completedAt: number | null;
    createdAt: number;

    notifyBefore: string;

    timezone: string;
};
