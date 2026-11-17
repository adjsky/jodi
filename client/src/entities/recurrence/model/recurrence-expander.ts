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

import { getDiffInDays } from "../helpers/get-diff-in-days.ts";

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
    recurrenceableId: string;
    occursAt: string;
    isCancelled: boolean;
    overrides: Partial<T>;
};

type RecurrenceConfig<StartKey, EndKey, ShiftedDateKeys> = {
    startKey: StartKey;
    endKey: EndKey;
    shiftedDateKeys: ShiftedDateKeys;
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
        recurrenceExceptions: RecurrenceException<NoInfer<T>>[]
    ): WithRecurrence<T>[] {
        const { timezone } = this.#config;
        const view: ViewRange = {
            start: toZoned(date, timezone),
            end: toZoned(date.add({ days: 1 }), timezone)
        };
        const exceptionsPerId = groupBy(
            recurrenceExceptions,
            prop("recurrenceableId")
        );

        return items.flatMap((item) =>
            this.#expandItem(item, exceptionsPerId[item.id] ?? [], view)
        );
    }

    #expandItem<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        item: T,
        itemExceptions: RecurrenceException<T>[],
        view: ViewRange
    ): WithRecurrence<T>[] {
        if (item.rrule === null) {
            return [{ ...item, occursAt: null, recurringSince: null }];
        }

        const { startKey, endKey } = this.#config;

        const start = toTimeZone(item[startKey], UTC);
        const end = endKey ? toTimeZone(item[endKey], UTC) : null;

        const dates: RecurrenceDates = {
            start,
            end,
            edoffset: end ? getDiffInDays(end, start) : 0
        };

        const candidates = this.#generateCandidates(item.rrule, dates, view);
        const exceptions = new Map(itemExceptions.map((e) => [e.occursAt, e]));

        this.#applyExceptions(candidates, exceptions, dates, view);

        return Array.from(candidates, ([occursAt, date]) =>
            this.#createOccurrence(
                item,
                occursAt,
                date,
                dates.start,
                exceptions.get(occursAt)
            )
        ).filter((occurrence) =>
            this.#overlaps(
                occurrence[startKey],
                endKey ? occurrence[endKey] : null,
                view
            )
        );
    }

    #generateCandidates(
        rule: string,
        dates: RecurrenceDates,
        view: ViewRange
    ): Map<string, Date> {
        const start = toTimeZone(view.start, UTC).subtract({
            days: dates.edoffset
        });

        const rrule = new RRule({
            ...RRule.parseString(rule),
            dtstart: dates.start.toDate()
        });
        const candidates = rrule.between(
            start.toDate(),
            view.end.toDate(),
            true
        );

        return new Map(
            candidates.map((date) => [
                toCalendarDate(fromDate(date, UTC)).toString(),
                date
            ])
        );
    }

    #applyExceptions<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        candidates: Map<string, Date>,
        exceptions: Map<string, RecurrenceException<T>>,
        dates: RecurrenceDates,
        view: ViewRange
    ): void {
        const { startKey, endKey } = this.#config;

        for (const [occursAt, exception] of exceptions) {
            if (exception.isCancelled) {
                candidates.delete(occursAt);
                continue;
            }

            const startOverride = exception.overrides[startKey];
            const endOverride = endKey ? exception.overrides[endKey] : null;

            if (!startOverride && !endOverride) {
                continue;
            }

            const date = parseDate(occursAt);
            const start =
                startOverride ??
                toZoned(toCalendarDateTime(date, dates.start), UTC);
            const end = dates.end
                ? (endOverride ??
                  dates.end.set(date.add({ days: dates.edoffset })))
                : null;

            if (this.#overlaps(start, end, view)) {
                candidates.set(occursAt, date.toDate(UTC));
            } else {
                candidates.delete(occursAt);
            }
        }
    }

    #createOccurrence<T extends Expandable<StartKey, EndKey, ShiftedDateKeys>>(
        item: T,
        occursAt: string,
        date: Date,
        dtstart: ZonedDateTime,
        exception?: RecurrenceException<T>
    ): WithRecurrence<T> {
        const { startKey, endKey, shiftedDateKeys } = this.#config;

        const dateKeys = [
            startKey,
            ...(endKey ? [endKey] : []),
            ...shiftedDateKeys
        ];

        const occurrence = {
            ...item,
            occursAt: parseDate(occursAt),
            recurringSince: toCalendarDate(dtstart),
            ...dateKeys.reduce(
                (acc, key) => {
                    if (exception?.overrides?.[key]) {
                        return acc;
                    }

                    const value = item[key];

                    if (!value) {
                        return acc;
                    }

                    const utcValue = toTimeZone(value, UTC);
                    const offset = getDiffInDays(utcValue, dtstart);

                    acc[key] = toTimeZone(
                        fromDate(date, UTC)
                            .add({ days: offset })
                            .set(toTime(utcValue)),
                        value.timeZone
                    );

                    return acc;
                },
                {} as Record<string, ZonedDateTime>
            )
        };

        return Object.assign(occurrence, exception?.overrides);
    }

    #overlaps(
        start: ZonedDateTime,
        end: ZonedDateTime | null,
        view: ViewRange
    ): boolean {
        if (end) {
            return start.compare(view.end) < 0 && end.compare(view.start) > 0;
        }

        return start.compare(view.start) >= 0 && start.compare(view.end) < 0;
    }
}
