import {
    DateFormatter,
    getWeeksInMonth,
    startOfWeek,
    startOfYear,
    today
} from "@internationalized/date";
import { TIMEZONE, WEEK_START_MAP } from "$/shared/cfg/constants";
import { extract, watch } from "runed";

import { getWeekDays } from "./get-week-days";

import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "$/paraglide/runtime";
import type { WeekStart } from "$/shared/lib/types";
import type { Getter, MaybeGetter } from "runed";

type Options = {
    weekStart: WeekStart;
    locale: Locale;
};

export class Year {
    #cursor: CalendarDate;
    #options: MaybeGetter<Options>;

    constructor(selected: Getter<CalendarDate>, options: MaybeGetter<Options>) {
        this.#cursor = $state(extract(selected));
        this.#options = options;

        watch(
            () => [extract(selected)],
            () => {
                this.#cursor = extract(selected);
            },
            { lazy: true }
        );
    }

    get current() {
        return this.#cursor.year;
    }

    set current(year: number) {
        this.#cursor = this.#cursor.set({ year });
    }

    months() {
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

    weeks(date: CalendarDate) {
        const nWeeks = this.#weeksInMonth(date);
        const start = this.#startOfWeek(date);

        return Array.from({ length: nWeeks }).map((_, idx) =>
            getWeekDays(start.add({ weeks: idx })).map((d) => ({
                isWithinMonth: d.month == date.month,
                date: d
            }))
        );
    }

    weekdays() {
        const formatter = new DateFormatter(extract(this.#options).locale, {
            weekday: "short"
        });

        return getWeekDays(this.#startOfWeek(today(TIMEZONE))).map((date) =>
            formatter.format(date.toDate(TIMEZONE))
        );
    }

    next() {
        this.#cursor = startOfYear(this.#cursor.add({ years: 1 }));
    }

    previous() {
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
