import {
    DateFormatter,
    getWeeksInMonth,
    startOfWeek,
    startOfYear,
    today
} from "@internationalized/date";
import { TIMEZONE, WEEK_START_MAP } from "#shared/cfg/constants.ts";
import { get_week_days } from "#shared/lib/date/get-week-days.ts";
import { extract, watch } from "runed";

import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "#paraglide/runtime.js";
import type { WeekStart } from "#shared/lib/date/types.ts";
import type { Getter, MaybeGetter } from "runed";

type Options = {
    week_start: WeekStart;
    locale: Locale;
};

type Month = { name: string; date: CalendarDate };

type Day = { is_within_month: boolean; date: CalendarDate };

export class Year {
    #options: MaybeGetter<Options>;
    #cursor: CalendarDate;

    constructor(selected: Getter<CalendarDate>, options: MaybeGetter<Options>) {
        this.#options = options;
        this.#cursor = $state(extract(selected));

        watch(
            () => [extract(selected)],
            () => {
                this.#cursor = extract(selected);
            },
            { lazy: true }
        );
    }

    get current(): number {
        return this.#cursor.year;
    }

    set current(year: number) {
        this.#cursor = this.#cursor.set({ year });
    }

    months(): Month[] {
        const start = startOfYear(this.#cursor);
        const formatter = new DateFormatter(extract(this.#options).locale, {
            month: "long",
            timeZone: TIMEZONE
        });

        return Array.from({ length: 12 }).map((_, idx) => {
            const date = start.add({ months: idx });
            return {
                name: formatter.format(date.toDate(TIMEZONE)),
                date
            };
        });
    }

    weeks(date: CalendarDate): Day[][] {
        const nweeks = this.#weeks_in_month(date);
        const start = this.#start_of_week(date);

        return Array.from({ length: nweeks }).map((_, idx) =>
            get_week_days(start.add({ weeks: idx })).map((d) => ({
                is_within_month: d.month === date.month,
                date: d
            }))
        );
    }

    weekdays(): string[] {
        const formatter = new DateFormatter(extract(this.#options).locale, {
            weekday: "short",
            timeZone: TIMEZONE
        });

        return get_week_days(this.#start_of_week(today(TIMEZONE))).map((date) =>
            formatter.format(date.toDate(TIMEZONE))
        );
    }

    next(): void {
        this.#cursor = startOfYear(this.#cursor.add({ years: 1 }));
    }

    previous(): void {
        this.#cursor = startOfYear(this.#cursor.subtract({ years: 1 }));
    }

    #start_of_week(date: CalendarDate) {
        return startOfWeek(
            date,
            extract(this.#options).locale,
            WEEK_START_MAP[extract(this.#options).week_start]
        );
    }

    #weeks_in_month(date: CalendarDate) {
        return getWeeksInMonth(
            date,
            extract(this.#options).locale,
            WEEK_START_MAP[extract(this.#options).week_start]
        );
    }
}
