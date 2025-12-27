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
import type { DateValue } from "@internationalized/date";
import type { Getter } from "runed";

export class Year {
    #cursor: DateValue;
    #start: Getter<WeekStart>;

    constructor(selected: DateValue, start: Getter<WeekStart>) {
        this.#cursor = $state(selected);
        this.#start = start;
    }

    get current() {
        return this.#cursor.year;
    }

    months() {
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

    weeks(date: DateValue) {
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

    #startOfWeek(date: DateValue) {
        return startOfWeek(date, getLocale(), DMAP[extract(this.#start)]);
    }

    #weeksInMonth(date: DateValue) {
        return getWeeksInMonth(date, getLocale(), DMAP[extract(this.#start)]);
    }
}
