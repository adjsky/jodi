import { parseDate, parseZonedDateTime } from "@internationalized/date";
import { expect, it } from "vitest";

import { RecurrenceExpander } from "./recurrence-expander.ts";

import type { ZonedDateTime } from "@internationalized/date";

it("clones nonrecurring items with empty recurrence metadata", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });
    const original = {
        id: "item-1",
        rrule: null,
        scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
    };

    const occurrences = expander.on(parseDate("2030-01-08"), [original], []);

    expect(occurrences).toEqual([
        { ...original, occursAt: null, recurringSince: null }
    ]);
    expect(occurrences[0]).not.toBe(original);
});

it("lists a recurring todo using its scheduled date", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-08"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
            }
        ],
        []
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=WEEKLY",
            scheduledAt: parseZonedDateTime("2030-01-08T18:00[UTC]"),
            occursAt: parseDate("2030-01-08"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("expands recurring single-day events while preserving the input", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });
    const original = {
        id: "item-1",
        rrule: "FREQ=WEEKLY",
        startsAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
        endsAt: parseZonedDateTime("2030-01-01T20:00[UTC]")
    };

    const occurrences = expander.on(parseDate("2030-01-08"), [original], []);

    expect(occurrences).toEqual([
        {
            ...original,
            startsAt: parseZonedDateTime("2030-01-08T18:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-08T20:00[UTC]"),
            occursAt: parseDate("2030-01-08"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
    expect(original.startsAt).toEqual(
        parseZonedDateTime("2030-01-01T18:00[UTC]")
    );
});

it("applies cancellations only to the matching item and occurrence", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-08"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
            },
            {
                id: "item-2",
                rrule: "FREQ=WEEKLY",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-01",
                isCancelled: true,
                overrides: {}
            },
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-08",
                isCancelled: true,
                overrides: {}
            }
        ]
    );

    expect(occurrences).toEqual([
        {
            id: "item-2",
            rrule: "FREQ=WEEKLY",
            scheduledAt: parseZonedDateTime("2030-01-08T18:00[UTC]"),
            occursAt: parseDate("2030-01-08"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("includes an occurrence moved into the day beyond the recurrence limit", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-09"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY;COUNT=1",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-01",
                isCancelled: false,
                overrides: {
                    scheduledAt: parseZonedDateTime("2030-01-09T18:00[UTC]")
                }
            }
        ]
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=WEEKLY;COUNT=1",
            scheduledAt: parseZonedDateTime("2030-01-09T18:00[UTC]"),
            occursAt: parseDate("2030-01-01"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("excludes an occurrence moved out of the day", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-08"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-08",
                isCancelled: false,
                overrides: {
                    scheduledAt: parseZonedDateTime("2030-01-09T18:00[UTC]")
                }
            }
        ]
    );

    expect(occurrences).toEqual([]);
});

it("includes recurring multi-day events on continuation days", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-09"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY",
                startsAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
                endsAt: parseZonedDateTime("2030-01-03T09:00[UTC]")
            }
        ],
        []
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=WEEKLY",
            startsAt: parseZonedDateTime("2030-01-08T18:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-10T09:00[UTC]"),
            occursAt: parseDate("2030-01-08"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("includes occurrences extended into the day by an end-only override", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-05"),
        [
            {
                id: "item-1",
                rrule: "FREQ=DAILY",
                startsAt: parseZonedDateTime("2030-01-01T10:00[UTC]"),
                endsAt: parseZonedDateTime("2030-01-01T11:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-01",
                isCancelled: false,
                overrides: {
                    endsAt: parseZonedDateTime("2030-01-10T11:00[UTC]")
                }
            }
        ]
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=DAILY",
            startsAt: parseZonedDateTime("2030-01-05T10:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-05T11:00[UTC]"),
            occursAt: parseDate("2030-01-05"),
            recurringSince: parseDate("2030-01-01")
        },
        {
            id: "item-1",
            rrule: "FREQ=DAILY",
            startsAt: parseZonedDateTime("2030-01-01T10:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-10T11:00[UTC]"),
            occursAt: parseDate("2030-01-01"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("excludes events ending at the day's start and points at the next midnight", () => {
    const eventExpander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });
    const pointExpander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });
    const original = {
        id: "item-1",
        rrule: "FREQ=DAILY",
        startsAt: parseZonedDateTime("2030-01-01T00:00[UTC]"),
        endsAt: parseZonedDateTime("2030-01-02T00:00[UTC]")
    };

    const events = eventExpander.on(parseDate("2030-01-02"), [original], []);
    const points = pointExpander.on(parseDate("2030-01-02"), [original], []);

    expect(events).toEqual([
        {
            ...original,
            startsAt: parseZonedDateTime("2030-01-02T00:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-03T00:00[UTC]"),
            occursAt: parseDate("2030-01-02"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
    expect(points).toEqual([
        {
            ...original,
            startsAt: parseZonedDateTime("2030-01-02T00:00[UTC]"),
            occursAt: parseDate("2030-01-02"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("shifts UTC calendar dates while preserving each attribute's timezone", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: ["reminderAt"],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2026-03-08"),
        [
            {
                id: "item-1",
                rrule: "FREQ=DAILY",
                startsAt: parseZonedDateTime(
                    "2026-03-07T09:00[America/New_York]"
                ),
                endsAt: parseZonedDateTime(
                    "2026-03-07T10:00[America/New_York]"
                ),
                reminderAt: parseZonedDateTime(
                    "2026-03-06T20:00[America/New_York]"
                )
            }
        ],
        []
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=DAILY",
            startsAt: parseZonedDateTime("2026-03-08T10:00[America/New_York]"),
            endsAt: parseZonedDateTime("2026-03-08T11:00[America/New_York]"),
            reminderAt: parseZonedDateTime(
                "2026-03-07T20:00[America/New_York]"
            ),
            occursAt: parseDate("2026-03-08"),
            recurringSince: parseDate("2026-03-07")
        }
    ]);
});

it("applies typed completion and null overrides without shifting completion", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: ["reminderAt"],
        timezone: "UTC"
    });
    const original: {
        id: string;
        title: string;
        rrule: string;
        scheduledAt: ZonedDateTime;
        reminderAt: ZonedDateTime | null;
        completedAt: ZonedDateTime | null;
    } = {
        id: "item-1",
        title: "Original",
        rrule: "FREQ=WEEKLY",
        scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
        reminderAt: parseZonedDateTime("2030-01-01T17:00[UTC]"),
        completedAt: null
    };

    const occurrences = expander.on(
        parseDate("2030-01-08"),
        [original],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-08",
                isCancelled: false,
                overrides: {
                    title: "Done",
                    completedAt: parseZonedDateTime("2030-01-09T11:00[UTC]"),
                    reminderAt: null
                }
            }
        ]
    );

    expect(occurrences).toEqual([
        {
            ...original,
            title: "Done",
            scheduledAt: parseZonedDateTime("2030-01-08T18:00[UTC]"),
            completedAt: parseZonedDateTime("2030-01-09T11:00[UTC]"),
            reminderAt: null,
            occursAt: parseDate("2030-01-08"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
    expect(original.completedAt).toBeNull();
    expect(original.reminderAt).not.toBeNull();
});

it("excludes current and future occurrences using a cancellation and UNTIL", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });
    const items = [
        {
            id: "item-1",
            rrule: "FREQ=DAILY;UNTIL=20300108T235959Z",
            scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
        }
    ];
    const exceptions = [
        {
            recurrenceableId: "item-1",
            occursAt: "2030-01-08",
            isCancelled: true,
            overrides: {}
        }
    ];

    const before = expander.on(parseDate("2030-01-07"), items, exceptions);
    const current = expander.on(parseDate("2030-01-08"), items, exceptions);
    const after = expander.on(parseDate("2030-01-09"), items, exceptions);

    expect(before).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=DAILY;UNTIL=20300108T235959Z",
            scheduledAt: parseZonedDateTime("2030-01-07T18:00[UTC]"),
            occursAt: parseDate("2030-01-07"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
    expect(current).toEqual([]);
    expect(after).toEqual([]);
});

it("preserves completion exceptions on both sides of a series split", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });
    const items: {
        id: string;
        rrule: string;
        scheduledAt: ZonedDateTime;
        completedAt: ZonedDateTime | null;
    }[] = [
        {
            id: "item-1",
            rrule: "FREQ=DAILY;UNTIL=20260104T235959Z",
            scheduledAt: parseZonedDateTime("2026-01-01T10:00[UTC]"),
            completedAt: null
        },
        {
            id: "item-2",
            rrule: "FREQ=WEEKLY",
            scheduledAt: parseZonedDateTime("2026-01-05T10:00[UTC]"),
            completedAt: null
        }
    ];
    const exceptions = [
        {
            recurrenceableId: "item-1",
            occursAt: "2026-01-02",
            isCancelled: false,
            overrides: {
                completedAt: parseZonedDateTime("2026-01-02T11:22:33[UTC]")
            }
        },
        {
            recurrenceableId: "item-2",
            occursAt: "2026-01-05",
            isCancelled: false,
            overrides: {
                completedAt: parseZonedDateTime("2026-01-05T11:22:33[UTC]")
            }
        }
    ];

    const before = expander.on(parseDate("2026-01-02"), items, exceptions);
    const lastOriginal = expander.on(
        parseDate("2026-01-04"),
        items,
        exceptions
    );
    const after = expander.on(parseDate("2026-01-05"), items, exceptions);

    expect(before).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=DAILY;UNTIL=20260104T235959Z",
            scheduledAt: parseZonedDateTime("2026-01-02T10:00[UTC]"),
            completedAt: parseZonedDateTime("2026-01-02T11:22:33[UTC]"),
            occursAt: parseDate("2026-01-02"),
            recurringSince: parseDate("2026-01-01")
        }
    ]);
    expect(lastOriginal).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=DAILY;UNTIL=20260104T235959Z",
            scheduledAt: parseZonedDateTime("2026-01-04T10:00[UTC]"),
            completedAt: null,
            occursAt: parseDate("2026-01-04"),
            recurringSince: parseDate("2026-01-01")
        }
    ]);
    expect(after).toEqual([
        {
            id: "item-2",
            rrule: "FREQ=WEEKLY",
            scheduledAt: parseZonedDateTime("2026-01-05T10:00[UTC]"),
            completedAt: parseZonedDateTime("2026-01-05T11:22:33[UTC]"),
            occursAt: parseDate("2026-01-05"),
            recurringSince: parseDate("2026-01-05")
        }
    ]);
});

it("preserves a multi-day event's default end when only its start is overridden", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-03"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY;COUNT=1",
                startsAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
                endsAt: parseZonedDateTime("2030-01-03T09:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-01",
                isCancelled: false,
                overrides: {
                    startsAt: parseZonedDateTime("2030-01-03T06:00[UTC]")
                }
            }
        ]
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=WEEKLY;COUNT=1",
            startsAt: parseZonedDateTime("2030-01-03T06:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-03T09:00[UTC]"),
            occursAt: parseDate("2030-01-01"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("excludes a continuation shortened to the day boundary by an end-only override", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-09"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY",
                startsAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
                endsAt: parseZonedDateTime("2030-01-03T09:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-08",
                isCancelled: false,
                overrides: {
                    endsAt: parseZonedDateTime("2030-01-09T00:00[UTC]")
                }
            }
        ]
    );

    expect(occurrences).toEqual([]);
});

it("returns every naturally overlapping occurrence from a daily multi-day series", () => {
    const expander = new RecurrenceExpander({
        startKey: "startsAt",
        endKey: "endsAt",
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-04"),
        [
            {
                id: "item-1",
                rrule: "FREQ=DAILY",
                startsAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
                endsAt: parseZonedDateTime("2030-01-03T09:00[UTC]")
            }
        ],
        []
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=DAILY",
            startsAt: parseZonedDateTime("2030-01-02T18:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-04T09:00[UTC]"),
            occursAt: parseDate("2030-01-02"),
            recurringSince: parseDate("2030-01-01")
        },
        {
            id: "item-1",
            rrule: "FREQ=DAILY",
            startsAt: parseZonedDateTime("2030-01-03T18:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-05T09:00[UTC]"),
            occursAt: parseDate("2030-01-03"),
            recurringSince: parseDate("2030-01-01")
        },
        {
            id: "item-1",
            rrule: "FREQ=DAILY",
            startsAt: parseZonedDateTime("2030-01-04T18:00[UTC]"),
            endsAt: parseZonedDateTime("2030-01-06T09:00[UTC]"),
            occursAt: parseDate("2030-01-04"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("does not create an occurrence from a non-date override on a missing occurrence", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-09"),
        [
            {
                id: "item-1",
                title: "Original",
                rrule: "FREQ=WEEKLY",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]")
            }
        ],
        [
            {
                recurrenceableId: "item-1",
                occursAt: "2030-01-09",
                isCancelled: false,
                overrides: { title: "Updated" }
            }
        ]
    );

    expect(occurrences).toEqual([]);
});

it("preserves null shifted attributes without an exception", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: ["reminderAt"],
        timezone: "UTC"
    });

    const occurrences = expander.on(
        parseDate("2030-01-08"),
        [
            {
                id: "item-1",
                rrule: "FREQ=WEEKLY",
                scheduledAt: parseZonedDateTime("2030-01-01T18:00[UTC]"),
                reminderAt: null
            }
        ],
        []
    );

    expect(occurrences).toEqual([
        {
            id: "item-1",
            rrule: "FREQ=WEEKLY",
            scheduledAt: parseZonedDateTime("2030-01-08T18:00[UTC]"),
            reminderAt: null,
            occursAt: parseDate("2030-01-08"),
            recurringSince: parseDate("2030-01-01")
        }
    ]);
});

it("uses the viewing timezone's day boundaries across a DST transition", () => {
    const expander = new RecurrenceExpander({
        startKey: "scheduledAt",
        endKey: null,
        shiftedDateKeys: [],
        timezone: "America/New_York"
    });

    const occurrences = expander.on(
        parseDate("2026-03-08"),
        [
            {
                id: "before-start",
                rrule: "FREQ=DAILY;COUNT=1",
                scheduledAt: parseZonedDateTime("2026-03-08T04:59[UTC]")
            },
            {
                id: "at-start",
                rrule: "FREQ=DAILY;COUNT=1",
                scheduledAt: parseZonedDateTime("2026-03-08T05:00[UTC]")
            },
            {
                id: "before-end",
                rrule: "FREQ=DAILY;COUNT=1",
                scheduledAt: parseZonedDateTime("2026-03-09T03:59[UTC]")
            },
            {
                id: "at-end",
                rrule: "FREQ=DAILY;COUNT=1",
                scheduledAt: parseZonedDateTime("2026-03-09T04:00[UTC]")
            }
        ],
        []
    );

    expect(occurrences).toEqual([
        {
            id: "at-start",
            rrule: "FREQ=DAILY;COUNT=1",
            scheduledAt: parseZonedDateTime("2026-03-08T05:00[UTC]"),
            occursAt: parseDate("2026-03-08"),
            recurringSince: parseDate("2026-03-08")
        },
        {
            id: "before-end",
            rrule: "FREQ=DAILY;COUNT=1",
            scheduledAt: parseZonedDateTime("2026-03-09T03:59[UTC]"),
            occursAt: parseDate("2026-03-09"),
            recurringSince: parseDate("2026-03-09")
        }
    ]);
});
