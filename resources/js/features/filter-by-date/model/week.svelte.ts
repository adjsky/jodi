import { extract } from "runed";

import type { Dayjs } from "dayjs";
import type { Getter } from "runed";

export type WeekStart = "monday" | "sunday";

const DMAP: { [D in WeekStart]: number } = { monday: 1, sunday: 0 };

export class Week {
    #day: Getter<Dayjs>;
    #start: Getter<WeekStart>;

    constructor(day: Getter<Dayjs>, start: Getter<WeekStart>) {
        this.#day = day;
        this.#start = start;
    }

    get days() {
        const start = this.#startOfWeek(extract(this.#day));
        const days = [] as Dayjs[];

        for (let i = 0; i < 7; i++) {
            const day = start.add(i, "day");
            days.push(day);
        }

        return days;
    }

    next() {
        return this.#startOfWeek(extract(this.#day).add(1, "week"));
    }

    previous() {
        return this.#startOfWeek(extract(this.#day).subtract(1, "week"));
    }

    #startOfWeek(day: Dayjs) {
        const actualWeekDay = day.day();
        const expectedWeekDay = DMAP[extract(this.#start)];

        const diff =
            actualWeekDay < expectedWeekDay
                ? 7 - (expectedWeekDay - actualWeekDay)
                : actualWeekDay - expectedWeekDay;

        return day.subtract(diff, "day");
    }
}
