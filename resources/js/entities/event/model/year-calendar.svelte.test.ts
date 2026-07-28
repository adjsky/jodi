import { expect, it } from "vitest";

import { YearCalendar } from "./year-calendar.svelte";

it("returns no weeks for a month that has not been prepared", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    expect(calendar.segments(2026, 1, 1)).toEqual([]);
});

it("positions a single-day event in its weekday column", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Dentist",
                color: "#38bdf8",
                startsAt: "2026-01-08T10:00:00Z",
                endsAt: "2026-01-08T11:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        {
            id: "1",
            title: "Dentist",
            color: "#38bdf8",
            column: 4,
            span: 1,
            lane: 0,
            startsInWeek: true,
            endsInWeek: true
        }
    ]);
});

it("positions a multi-day event in its weekday column", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Event",
                color: "#38bdf8",
                startsAt: "2026-01-08T10:00:00Z",
                endsAt: "2026-01-10T11:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        {
            id: "1",
            title: "Event",
            color: "#38bdf8",
            column: 4,
            span: 3,
            lane: 0,
            startsInWeek: true,
            endsInWeek: true
        }
    ]);
});

it("splits a cross-week event into separate segments", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Conference",
                color: null,
                startsAt: "2026-01-09T09:00:00Z",
                endsAt: "2026-01-20T18:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        {
            id: "1",
            title: "Conference",
            color: null,
            column: 5,
            span: 3,
            lane: 0,
            startsInWeek: true,
            endsInWeek: false
        }
    ]);
    expect(calendar.segments(2026, 1, 3)).toEqual([
        {
            id: "1",
            title: "Conference",
            color: null,
            column: 1,
            span: 7,
            lane: 0,
            startsInWeek: false,
            endsInWeek: false
        }
    ]);
    expect(calendar.segments(2026, 1, 4)).toEqual([
        {
            id: "1",
            title: "Conference",
            color: null,
            column: 1,
            span: 2,
            lane: 0,
            startsInWeek: false,
            endsInWeek: true
        }
    ]);
});

it("positions events when weeks start on Sunday", () => {
    const calendar = new YearCalendar({
        locale: "en",
        weekStart: "sunday"
    });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Sunday-start meeting",
                color: null,
                startsAt: "2026-01-08T10:00:00Z",
                endsAt: "2026-01-08T11:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ column: 5, span: 1 })
    ]);
});

it("includes events from outside the month in its rendered week grid", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Year-end preparation",
                color: null,
                startsAt: "2025-12-30T09:00:00Z",
                endsAt: "2025-12-31T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 1)).toEqual([
        expect.objectContaining({
            column: 2,
            span: 2,
            startsInWeek: true,
            endsInWeek: true
        })
    ]);
});

it("renders a cross-month event when only its ending month is requested", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Holiday break",
                color: null,
                startsAt: "2025-12-30T09:00:00Z",
                endsAt: "2026-01-05T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 1)).toEqual([
        expect.objectContaining({ column: 2, span: 6 })
    ]);
    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ column: 1, span: 1 })
    ]);
});

it("renders a cross-month event when only its starting month is requested", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Project rollout",
                color: null,
                startsAt: "2026-01-30T09:00:00Z",
                endsAt: "2026-02-05T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 5)).toEqual([
        expect.objectContaining({ column: 5, span: 3 })
    ]);
});

it("renders a cross-year event in both requested years", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });
    const crossYearEvent = {
        id: 1,
        title: "Winter vacation",
        color: null,
        startsAt: "2025-12-30T09:00:00Z",
        endsAt: "2026-01-05T10:00:00Z",
        occursAt: null
    };

    calendar.prepare(2025, [12], [crossYearEvent]);
    calendar.prepare(2026, [1], [crossYearEvent]);

    expect(calendar.segments(2025, 12, 5)).toEqual([
        expect.objectContaining({ column: 2, span: 6 })
    ]);
    expect(calendar.segments(2026, 1, 1)).toEqual([
        expect.objectContaining({ column: 2, span: 6 })
    ]);
    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ column: 1, span: 1 })
    ]);
});

it("handles events ending on the last day of a week", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Full-week retreat",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-11T23:59:59Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({
            column: 1,
            span: 7,
            startsInWeek: true,
            endsInWeek: true
        })
    ]);
});

it("handles events crossing from the last day into the first day of a week", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Weekend handoff",
                color: null,
                startsAt: "2026-01-11T09:00:00Z",
                endsAt: "2026-01-12T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ column: 7, span: 1 })
    ]);
    expect(calendar.segments(2026, 1, 3)).toEqual([
        expect.objectContaining({ column: 1, span: 1 })
    ]);
});

it("excludes the day at an event's midnight end boundary", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Midnight deployment",
                color: null,
                startsAt: "2026-01-05T10:00:00Z",
                endsAt: "2026-01-06T00:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ column: 1, span: 1 })
    ]);
});

it("assigns lanes and replaces stale overflow values", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Design review",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-07T10:00:00Z",
                occursAt: null
            },
            {
                id: 2,
                title: "Engineering review",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-07T10:00:00Z",
                occursAt: null
            },
            {
                id: 3,
                title: "Product review",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-07T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ lane: 0 }),
        expect.objectContaining({ lane: 1 }),
        expect.objectContaining({ lane: 2 })
    ]);
    expect(calendar.overflow(2026, 1, 2, 1)).toBe(1);
    expect(calendar.overflow(2026, 1, 2, 2)).toBe(1);
    expect(calendar.overflow(2026, 1, 2, 3)).toBe(1);
    expect(calendar.overflow(2026, 1, 2, 4)).toBe(0);

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Design review",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-07T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toHaveLength(1);
    expect(calendar.overflow(2026, 1, 2, 2)).toBe(0);
});

it("reuses a lane only when events do not share a day", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Early task",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-07T10:00:00Z",
                occursAt: null
            },
            {
                id: 2,
                title: "Follow-up task",
                color: null,
                startsAt: "2026-01-08T09:00:00Z",
                endsAt: "2026-01-09T10:00:00Z",
                occursAt: null
            },
            {
                id: 3,
                title: "Overlapping task",
                color: null,
                startsAt: "2026-01-09T12:00:00Z",
                endsAt: "2026-01-10T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ lane: 0 }),
        expect.objectContaining({ lane: 0 }),
        expect.objectContaining({ lane: 1 })
    ]);
});

it("clears an empty requested month and preserves other months", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "January appointment",
                color: null,
                startsAt: "2026-01-12T09:00:00Z",
                endsAt: "2026-01-12T10:00:00Z",
                occursAt: null
            }
        ]
    );
    calendar.prepare(
        2026,
        [2],
        [
            {
                id: 2,
                title: "February appointment",
                color: null,
                startsAt: "2026-02-10T09:00:00Z",
                endsAt: "2026-02-10T10:00:00Z",
                occursAt: null
            }
        ]
    );

    calendar.prepare(2026, [1], []);

    expect(calendar.segments(2026, 1, 3)).toEqual([]);
    expect(calendar.segments(2026, 2, 3)).toHaveLength(1);
});

it("replaces repeated and updated month data without duplicates", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Old title",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-05T10:00:00Z",
                occursAt: null
            }
        ]
    );
    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Old title",
                color: null,
                startsAt: "2026-01-05T09:00:00Z",
                endsAt: "2026-01-05T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toHaveLength(1);

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "New title",
                color: null,
                startsAt: "2026-01-08T09:00:00Z",
                endsAt: "2026-01-08T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 2)).toEqual([
        expect.objectContaining({ title: "New title", column: 4 })
    ]);
});

it("removes an event from its old month when it moves to another month", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 1,
                title: "Rescheduled appointment",
                color: null,
                startsAt: "2026-01-12T09:00:00Z",
                endsAt: "2026-01-12T10:00:00Z",
                occursAt: null
            }
        ]
    );

    calendar.prepare(
        2026,
        [1, 2],
        [
            {
                id: 1,
                title: "Rescheduled appointment",
                color: null,
                startsAt: "2026-02-10T09:00:00Z",
                endsAt: "2026-02-10T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 3)).toEqual([]);
    expect(calendar.segments(2026, 2, 3)).toHaveLength(1);
});

it("prepares month-specific segments from a batched response", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2026,
        [1, 2],
        [
            {
                id: 1,
                title: "January",
                color: null,
                startsAt: "2026-01-12T09:00:00Z",
                endsAt: "2026-01-12T10:00:00Z",
                occursAt: null
            },
            {
                id: 2,
                title: "February",
                color: null,
                startsAt: "2026-02-10T09:00:00Z",
                endsAt: "2026-02-10T10:00:00Z",
                occursAt: null
            },
            {
                id: 3,
                title: "Cross-month",
                color: null,
                startsAt: "2026-01-30T09:00:00Z",
                endsAt: "2026-02-05T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2026, 1, 3)).toEqual([
        expect.objectContaining({ title: "January" })
    ]);
    expect(calendar.segments(2026, 1, 5)).toEqual([
        expect.objectContaining({ title: "Cross-month" })
    ]);
    expect(calendar.segments(2026, 2, 1)).toEqual([
        expect.objectContaining({ title: "Cross-month" })
    ]);
    expect(calendar.segments(2026, 2, 2)).toEqual([
        expect.objectContaining({ title: "Cross-month" })
    ]);
    expect(calendar.segments(2026, 2, 3)).toEqual([
        expect.objectContaining({ title: "February" })
    ]);
});

it("preserves prepared months from other years", () => {
    const calendar = new YearCalendar({ locale: "en", weekStart: "monday" });

    calendar.prepare(
        2025,
        [12],
        [
            {
                id: 1,
                title: "December appointment",
                color: null,
                startsAt: "2025-12-10T09:00:00Z",
                endsAt: "2025-12-10T10:00:00Z",
                occursAt: null
            }
        ]
    );
    calendar.prepare(
        2026,
        [1],
        [
            {
                id: 2,
                title: "January appointment",
                color: null,
                startsAt: "2026-01-08T09:00:00Z",
                endsAt: "2026-01-08T10:00:00Z",
                occursAt: null
            }
        ]
    );

    expect(calendar.segments(2025, 12, 2)).toHaveLength(1);
    expect(calendar.segments(2026, 1, 2)).toHaveLength(1);
});
