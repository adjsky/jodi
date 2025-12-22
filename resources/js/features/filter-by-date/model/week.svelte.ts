import { startOfWeek } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";
import { extract } from "runed";

import { DMAP } from "../cfg/preferences";
import { getWeekDays } from "../helpers/date";

import type { WeekStart } from "../cfg/preferences";
import type { CalendarDate } from "@internationalized/date";
import type { Getter } from "runed";

export class Week {
    #cursor: Getter<CalendarDate>;
    #start: Getter<WeekStart>;

    constructor(cursor: Getter<CalendarDate>, start: Getter<WeekStart>) {
        this.#cursor = cursor;
        this.#start = start;
    }

    get days() {
        return getWeekDays(this.#startOfWeek(extract(this.#cursor)));
    }

    next() {
        return this.#startOfWeek(extract(this.#cursor).add({ weeks: 1 }));
    }

    previous() {
        return this.#startOfWeek(extract(this.#cursor).subtract({ weeks: 1 }));
    }

    #startOfWeek(date: CalendarDate) {
        return startOfWeek(date, getLocale(), DMAP[extract(this.#start)]);
    }
}
