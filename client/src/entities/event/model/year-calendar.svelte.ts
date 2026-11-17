import {
    getDayOfWeek,
    parseAbsoluteToLocal,
    startOfWeek,
    toCalendarDate
} from "@internationalized/date";
import { WEEK_START_MAP } from "#shared/cfg/constants.ts";
import { get_week_idx_in_month } from "#shared/lib/date/get-week-idx-in-month.ts";
import { extract } from "runed";
import { SvelteMap } from "svelte/reactivity";

import type { CalendarEventData } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "#paraglide/runtime";
import type { WeekStart } from "$/shared/lib/date/types";
import type { MaybeGetter } from "runed";

const DEFAULT_MAX_LANES = 2;

type Options = {
    week_start: WeekStart;
    locale: Locale;
    max_lanes?: number;
};

type SegmentPosition = {
    column: number;
    span: number;
    starts_in_week: boolean;
    ends_in_week: boolean;
};

type Segment = SegmentPosition & {
    id: string;
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

    segments(year: number, month: number, week: number): Segment[] {
        return this.#segments.get(year)?.get(month)?.[week - 1] ?? [];
    }

    visible_segments(year: number, month: number, week: number): Segment[] {
        const max_lanes = extract(this.#options).max_lanes ?? DEFAULT_MAX_LANES;
        const segments = this.segments(year, month, week);
        return segments.filter((segment) => segment.lane < max_lanes);
    }

    overflow(year: number, month: number, week: number, column: number): number {
        const max_lanes = extract(this.#options).max_lanes ?? DEFAULT_MAX_LANES;
        const segments = this.segments(year, month, week).filter(
            (segment) => segment.lane >= max_lanes && this.#segment_covers_column(segment, column)
        );
        return segments.length;
    }

    prepare(year: number, months: number[], events: CalendarEventData[]): void {
        const prepared_months = this.#create_months(months);

        for (const event of events) {
            this.#add_event(year, months, prepared_months, event);
        }

        this.#store(year, prepared_months);
    }

    #create_months(months: number[]) {
        const prepared_months = new Map<number, Segment[][]>();

        for (const month of months) {
            prepared_months.set(month, []);
        }

        return prepared_months;
    }

    #add_event(
        year: number,
        months: number[],
        prepared_months: Map<number, Segment[][]>,
        event: CalendarEventData
    ) {
        const options = extract(this.#options);
        const first_day_of_week = WEEK_START_MAP[options.week_start];

        const event_start = toCalendarDate(parseAbsoluteToLocal(event.startsAt));
        const event_end = toCalendarDate(
            parseAbsoluteToLocal(event.endsAt).subtract({ milliseconds: 1 })
        );

        let week_start = startOfWeek(event_start, options.locale, first_day_of_week);

        while (week_start.compare(event_end) <= 0) {
            const week_end = week_start.add({ days: 6 });

            const position = this.#calculate_segment_position(
                event_start,
                event_end,
                week_start,
                week_end
            );

            for (const month of new Set([week_start.month, week_end.month])) {
                const date = week_start.month === month ? week_start : week_end;

                if (date.year !== year || !months.includes(month)) {
                    continue;
                }

                const weekly_segments = prepared_months.get(month)!;

                const week_idx = get_week_idx_in_month(date, options.locale, options.week_start);

                weekly_segments[week_idx] ??= [];

                const week_segments = weekly_segments[week_idx];

                const lane = this.#calculate_segment_lane(week_segments, position);

                week_segments.push({
                    id: event.occursAt ? `${event.id}:${event.occursAt}` : `${event.id}`,
                    title: event.title,
                    color: event.color,
                    ...position,
                    lane
                });
            }

            week_start = week_start.add({ weeks: 1 });
        }
    }

    #calculate_segment_position(
        event_start: CalendarDate,
        event_end: CalendarDate,
        week_start: CalendarDate,
        week_end: CalendarDate
    ) {
        const options = extract(this.#options);
        const first_day_of_week = WEEK_START_MAP[options.week_start];

        const segment_start = event_start.compare(week_start) > 0 ? event_start : week_start;
        const segment_end = event_end.compare(week_end) < 0 ? event_end : week_end;

        const start_column = getDayOfWeek(segment_start, options.locale, first_day_of_week);
        const end_column = getDayOfWeek(segment_end, options.locale, first_day_of_week);

        const position: SegmentPosition = {
            column: start_column + 1,
            span: end_column - start_column + 1,
            starts_in_week: event_start.compare(week_start) >= 0,
            ends_in_week: event_end.compare(week_end) <= 0
        };

        return position;
    }

    #calculate_segment_lane(week_segments: Segment[], position: SegmentPosition) {
        const occupied_lanes = new Set(
            week_segments
                .filter((segment) => this.#segments_overlap(segment, position))
                .map((segment) => segment.lane)
        );

        let lane = 0;

        while (occupied_lanes.has(lane)) {
            lane++;
        }

        return lane;
    }

    #segments_overlap(a: SegmentPosition, b: SegmentPosition) {
        const a_end = a.column + a.span - 1;
        const b_end = b.column + b.span - 1;

        return a.column <= b_end && b.column <= a_end;
    }

    #segment_covers_column(segment: Segment, column: number) {
        const end_column = segment.column + segment.span - 1;

        return column >= segment.column && column <= end_column;
    }

    #store(year: number, prepared_months: Map<number, Segment[][]>) {
        let segments_by_month = this.#segments.get(year);

        if (!segments_by_month) {
            segments_by_month = new SvelteMap();
            this.#segments.set(year, segments_by_month);
        }

        for (const [month, weekly_segments] of prepared_months) {
            segments_by_month.set(month, weekly_segments);
        }
    }
}
