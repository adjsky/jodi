import {
    DateFormatter,
    getWeeksInMonth,
    startOfWeek,
    startOfYear,
    today
} from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";
import { TIMEZONE } from "$/shared/cfg/constants";
import { extract } from "runed";

import { DMAP } from "../cfg/preferences";
import { getWeekDays } from "../helpers/date";

import type { WeekStart } from "../cfg/preferences";
import type { CalendarDate } from "@internationalized/date";
import type { Getter } from "runed";

export type Month = { name: string; date: CalendarDate };

export class Year {
    #cursor: CalendarDate;
    #start: Getter<WeekStart>;

    constructor(selected: CalendarDate, start: Getter<WeekStart>) {
        this.#cursor = $state(selected);
        this.#start = start;
    }

    get current() {
        return this.#cursor.year;
    }

    months(): Month[] {
        const start = startOfYear(extract(this.#cursor));
        const formatter = new DateFormatter(getLocale(), { month: "long" });

        return Array.from({ length: 12 }).map((_, idx) => {
            const date = start.add({ months: idx });
            return {
                name: formatter.format(date.toDate(TIMEZONE)),
                date
            };
        });
    }

    weeks(month: Month) {
        const nWeeks = this.#weeksInMonth(month.date);
        const start = this.#startOfWeek(month.date);

        return Array.from({ length: nWeeks }).map((_, idx) =>
            getWeekDays(start.add({ weeks: idx })).map((date) => ({
                isWithinMonth: month.date.month == date.month,
                date
            }))
        );
    }

    weekdays() {
        const formatter = new DateFormatter(getLocale(), { weekday: "short" });
        return getWeekDays(this.#startOfWeek(today(TIMEZONE))).map((date) =>
            formatter.format(date.toDate(TIMEZONE))
        );
    }

    next() {
        this.#cursor = startOfYear(extract(this.#cursor).add({ years: 1 }));
    }

    previous() {
        this.#cursor = startOfYear(
            extract(this.#cursor).subtract({ years: 1 })
        );
    }

    #startOfWeek(date: CalendarDate) {
        return startOfWeek(date, getLocale(), DMAP[extract(this.#start)]);
    }

    #weeksInMonth(date: CalendarDate) {
        return getWeeksInMonth(date, getLocale(), DMAP[extract(this.#start)]);
    }
}
