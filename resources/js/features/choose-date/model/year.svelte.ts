import {
    DateFormatter,
    getWeeksInMonth,
    startOfWeek,
    startOfYear,
    today
} from "@internationalized/date";
import { TIMEZONE, WEEK_START_MAP } from "$/shared/cfg/constants";
import { getWeekDays } from "$/shared/lib/date/get-week-days";
import { extract, watch } from "runed";

import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "$/paraglide/runtime";
import type { WeekStart } from "$/shared/lib/date/types";
import type { Getter, MaybeGetter } from "runed";

type Options = {
    weekStart: WeekStart;
    locale: Locale;
};

type Month = { name: string; date: CalendarDate };

type Day = { isWithinMonth: boolean; date: CalendarDate };

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
            month: "long"
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
        const nWeeks = this.#weeksInMonth(date);
        const start = this.#startOfWeek(date);

        return Array.from({ length: nWeeks }).map((_, idx) =>
            getWeekDays(start.add({ weeks: idx })).map((d) => ({
                isWithinMonth: d.month == date.month,
                date: d
            }))
        );
    }

    weekdays(): string[] {
        const formatter = new DateFormatter(extract(this.#options).locale, {
            weekday: "short"
        });

        return getWeekDays(this.#startOfWeek(today(TIMEZONE))).map((date) =>
            formatter.format(date.toDate(TIMEZONE))
        );
    }

    next(): void {
        this.#cursor = startOfYear(this.#cursor.add({ years: 1 }));
    }

    previous(): void {
        this.#cursor = startOfYear(this.#cursor.subtract({ years: 1 }));
    }

    #startOfWeek(date: CalendarDate) {
        return startOfWeek(
            date,
            extract(this.#options).locale,
            WEEK_START_MAP[extract(this.#options).weekStart]
        );
    }

    #weeksInMonth(date: CalendarDate) {
        return getWeeksInMonth(
            date,
            extract(this.#options).locale,
            WEEK_START_MAP[extract(this.#options).weekStart]
        );
    }
}
