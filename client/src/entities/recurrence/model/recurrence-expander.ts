import {
    fromDate,
    parseDate,
    toCalendarDate,
    toCalendarDateTime,
    toTime,
    toTimeZone,
    toZoned
} from "@internationalized/date";
import { UTC } from "#shared/cfg/constants.ts";
import { groupBy, prop } from "remeda";
import { RRule } from "rrule";

import { get_diff_in_days } from "../helpers/get-diff-in-days.ts";

import type { WithRecurrence } from "./types.ts";
import type { CalendarDate, ZonedDateTime } from "@internationalized/date";

type Expandable<
    StartKey extends string,
    EndKey extends string | null,
    ShiftedDateKeys extends readonly string[]
> = {
    id: string;
    rrule: string | null;
} & Record<StartKey | NonNullable<EndKey>, ZonedDateTime> &
    Record<ShiftedDateKeys[number], ZonedDateTime | null>;

type RecurrenceException<T> = {
    recurrenceable_id: string;
    occurs_at: string;
    is_cancelled: boolean;
    overrides: Partial<T>;
};

type RecurrenceConfig<StartKey, EndKey, ShiftedDateKeys> = {
    start_key: StartKey;
    end_key: EndKey;
    shifted_date_keys: ShiftedDateKeys;
    timezone: string;
};

type ViewRange = {
    start: ZonedDateTime;
    end: ZonedDateTime;
};

type RecurrenceDates = {
    start: ZonedDateTime;
    end: ZonedDateTime | null;
    edoffset: number;
};

export class RecurrenceExpander<
    StartKey extends string,
    EndKey extends string | null,
    const ShiftedDateKeys extends readonly string[]
> {
    #config: RecurrenceConfig<StartKey, EndKey, ShiftedDateKeys>;

    constructor(config: RecurrenceConfig<StartKey, EndKey, ShiftedDateKeys>) {
        this.#config = config;
    }

    on<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        date: CalendarDate,
        items: T[],
        recurrence_exceptions: RecurrenceException<NoInfer<T>>[]
    ): WithRecurrence<T>[] {
        const { timezone } = this.#config;
        const view: ViewRange = {
            start: toZoned(date, timezone),
            end: toZoned(date.add({ days: 1 }), timezone)
        };
        const exceptions_per_id = groupBy(recurrence_exceptions, prop("recurrenceable_id"));

        return items.flatMap((item) =>
            this.#expand_item(item, exceptions_per_id[item.id] ?? [], view)
        );
    }

    #expand_item<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        item: T,
        item_exceptions: RecurrenceException<T>[],
        view: ViewRange
    ): WithRecurrence<T>[] {
        if (item.rrule === null) {
            return [{ ...item, occurs_at: null, recurring_since: null }];
        }

        const { start_key, end_key } = this.#config;

        const start = toTimeZone(item[start_key], UTC);
        const end = end_key ? toTimeZone(item[end_key], UTC) : null;

        const dates: RecurrenceDates = {
            start,
            end,
            edoffset: end ? get_diff_in_days(end, start) : 0
        };

        const candidates = this.#generate_candidates(item.rrule, dates, view);
        const exceptions = new Map(item_exceptions.map((e) => [e.occurs_at, e]));

        this.#apply_exceptions(candidates, exceptions, dates, view);

        return Array.from(candidates, ([occurs_at, date]) =>
            this.#create_occurrence(item, occurs_at, date, dates.start, exceptions.get(occurs_at))
        ).filter((occurrence) =>
            this.#overlaps(occurrence[start_key], end_key ? occurrence[end_key] : null, view)
        );
    }

    #generate_candidates(rule: string, dates: RecurrenceDates, view: ViewRange): Map<string, Date> {
        const start = toTimeZone(view.start, UTC).subtract({
            days: dates.edoffset
        });

        const rrule = new RRule({
            ...RRule.parseString(rule),
            dtstart: dates.start.toDate()
        });
        const candidates = rrule.between(start.toDate(), view.end.toDate(), true);

        return new Map(
            candidates.map((date) => [toCalendarDate(fromDate(date, UTC)).toString(), date])
        );
    }

    #apply_exceptions<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        candidates: Map<string, Date>,
        exceptions: Map<string, RecurrenceException<T>>,
        dates: RecurrenceDates,
        view: ViewRange
    ): void {
        const { start_key, end_key } = this.#config;

        for (const [occurs_at, exception] of exceptions) {
            if (exception.is_cancelled) {
                candidates.delete(occurs_at);
                continue;
            }

            const start_override = exception.overrides[start_key];
            const end_override = end_key ? exception.overrides[end_key] : null;

            if (!start_override && !end_override) {
                continue;
            }

            const date = parseDate(occurs_at);
            const start = start_override ?? toZoned(toCalendarDateTime(date, dates.start), UTC);
            const end = dates.end
                ? (end_override ?? dates.end.set(date.add({ days: dates.edoffset })))
                : null;

            if (this.#overlaps(start, end, view)) {
                candidates.set(occurs_at, date.toDate(UTC));
            } else {
                candidates.delete(occurs_at);
            }
        }
    }

    #create_occurrence<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        item: T,
        occurs_at: string,
        date: Date,
        dtstart: ZonedDateTime,
        exception?: RecurrenceException<T>
    ): WithRecurrence<T> {
        const { start_key, end_key, shifted_date_keys } = this.#config;

        const date_keys = [start_key, ...(end_key ? [end_key] : []), ...shifted_date_keys];

        const occurrence = {
            ...item,
            occurs_at: parseDate(occurs_at),
            recurring_since: toCalendarDate(dtstart),
            ...date_keys.reduce(
                (acc, key) => {
                    if (exception?.overrides?.[key]) {
                        return acc;
                    }

                    const value = item[key];

                    if (!value) {
                        return acc;
                    }

                    const utc_value = toTimeZone(value, UTC);
                    const offset = get_diff_in_days(utc_value, dtstart);

                    acc[key] = toTimeZone(
                        fromDate(date, UTC).add({ days: offset }).set(toTime(utc_value)),
                        value.timeZone
                    );

                    return acc;
                },
                {} as Record<string, ZonedDateTime>
            )
        };

        return Object.assign(occurrence, exception?.overrides);
    }

    #overlaps(start: ZonedDateTime, end: ZonedDateTime | null, view: ViewRange): boolean {
        if (end) {
            return start.compare(view.end) < 0 && end.compare(view.start) > 0;
        }

        return start.compare(view.start) >= 0 && start.compare(view.end) < 0;
    }
}
