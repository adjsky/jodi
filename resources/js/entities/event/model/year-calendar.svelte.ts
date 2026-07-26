import {
    getDayOfWeek,
    parseAbsoluteToLocal,
    startOfWeek,
    toCalendarDate
} from "@internationalized/date";
import { WEEK_START_MAP } from "$/shared/cfg/constants";
import { getWeekIdxInMonth } from "$/shared/lib/date/get-week-idx-in-month";
import { extract } from "runed";
import { SvelteMap } from "svelte/reactivity";

import type { CalendarEventData } from "./types";
import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "$/paraglide/runtime";
import type { WeekStart } from "$/shared/lib/types";
import type { MaybeGetter } from "runed";

const DEFAULT_MAX_LANES = 2;

type Options = {
    weekStart: WeekStart;
    locale: Locale;
    maxLanes?: number;
};

type SegmentPosition = {
    column: number;
    span: number;
    startsInWeek: boolean;
    endsInWeek: boolean;
};

type Segment = SegmentPosition & {
    eventId: number;
    title: string;
    color: string | null;
    lane: number;
};

export class YearCalendar {
    #segments = new SvelteMap<number, SvelteMap<number, Segment[][]>>();
    #options: MaybeGetter<Options>;

    constructor(options: MaybeGetter<Options>) {
        this.#options = options;
    }

    segments(year: number, month: number, week: number) {
        return this.#segments.get(year)?.get(month)?.[week - 1] ?? [];
    }

    visibleSegments(year: number, month: number, week: number) {
        const maxLanes = extract(this.#options).maxLanes ?? DEFAULT_MAX_LANES;
        const segments = this.segments(year, month, week);
        return segments.filter((segment) => segment.lane < maxLanes);
    }

    overflow(year: number, month: number, week: number, column: number) {
        const maxLanes = extract(this.#options).maxLanes ?? DEFAULT_MAX_LANES;
        const segments = this.segments(year, month, week).filter(
            (segment) =>
                segment.lane >= maxLanes &&
                this.#segmentCoversColumn(segment, column)
        );
        return segments.length;
    }

    prepare(year: number, months: number[], events: CalendarEventData[]) {
        const preparedMonths = this.#createMonths(months);

        for (const event of events) {
            this.#addEvent(preparedMonths, event, months);
        }

        this.#store(year, preparedMonths);
    }

    #createMonths(months: number[]) {
        const preparedMonths = new Map<number, Segment[][]>();

        for (const month of months) {
            preparedMonths.set(month, []);
        }

        return preparedMonths;
    }

    #addEvent(
        preparedMonths: Map<number, Segment[][]>,
        event: CalendarEventData,
        months: number[]
    ) {
        const options = extract(this.#options);
        const firstDayOfWeek = WEEK_START_MAP[options.weekStart];

        const eventStart = toCalendarDate(parseAbsoluteToLocal(event.startsAt));
        const eventEnd = toCalendarDate(
            parseAbsoluteToLocal(event.endsAt).subtract({ milliseconds: 1 })
        );

        let weekStart = startOfWeek(eventStart, options.locale, firstDayOfWeek);

        while (weekStart.compare(eventEnd) <= 0) {
            const weekEnd = weekStart.add({ days: 6 });

            const position = this.#calculateSegmentPosition(
                eventStart,
                eventEnd,
                weekStart,
                weekEnd
            );

            for (const month of new Set([weekStart.month, weekEnd.month])) {
                if (!months.includes(month)) {
                    continue;
                }

                const weeklySegments = preparedMonths.get(month)!;

                const weekIdx = getWeekIdxInMonth(
                    weekStart.month == month ? weekStart : weekEnd,
                    options.locale,
                    options.weekStart
                );

                weeklySegments[weekIdx] ??= [];

                const weekSegments = weeklySegments[weekIdx];

                const lane = this.#calculateSegmentLane(weekSegments, position);

                weekSegments.push({
                    eventId: event.id,
                    title: event.title,
                    color: event.color,
                    ...position,
                    lane
                });
            }

            weekStart = weekStart.add({ weeks: 1 });
        }
    }

    #calculateSegmentPosition(
        eventStart: CalendarDate,
        eventEnd: CalendarDate,
        weekStart: CalendarDate,
        weekEnd: CalendarDate
    ) {
        const options = extract(this.#options);
        const firstDayOfWeek = WEEK_START_MAP[options.weekStart];

        const segmentStart =
            eventStart.compare(weekStart) > 0 ? eventStart : weekStart;
        const segmentEnd = eventEnd.compare(weekEnd) < 0 ? eventEnd : weekEnd;

        const startColumn = getDayOfWeek(
            segmentStart,
            options.locale,
            firstDayOfWeek
        );
        const endColumn = getDayOfWeek(
            segmentEnd,
            options.locale,
            firstDayOfWeek
        );

        const position: SegmentPosition = {
            column: startColumn + 1,
            span: endColumn - startColumn + 1,
            startsInWeek: eventStart.compare(weekStart) >= 0,
            endsInWeek: eventEnd.compare(weekEnd) <= 0
        };

        return position;
    }

    #calculateSegmentLane(weekSegments: Segment[], position: SegmentPosition) {
        const occupiedLanes = new Set(
            weekSegments
                .filter((segment) => this.#segmentsOverlap(segment, position))
                .map((segment) => segment.lane)
        );

        let lane = 0;

        while (occupiedLanes.has(lane)) {
            lane++;
        }

        return lane;
    }

    #segmentsOverlap(a: SegmentPosition, b: SegmentPosition) {
        const aEnd = a.column + a.span - 1;
        const bEnd = b.column + b.span - 1;

        return a.column <= bEnd && b.column <= aEnd;
    }

    #segmentCoversColumn(segment: Segment, column: number) {
        const endColumn = segment.column + segment.span - 1;

        return column >= segment.column && column <= endColumn;
    }

    #store(year: number, preparedMonths: Map<number, Segment[][]>) {
        let segmentsByMonth = this.#segments.get(year);

        if (!segmentsByMonth) {
            segmentsByMonth = new SvelteMap();
            this.#segments.set(year, segmentsByMonth);
        }

        for (const [month, weeklySegments] of preparedMonths) {
            segmentsByMonth.set(month, weeklySegments);
        }
    }
}
