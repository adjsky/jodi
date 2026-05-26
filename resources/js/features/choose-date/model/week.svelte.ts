import { startOfWeek } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";
import { WEEK_START_PREFERENCE_MAP } from "$/shared/cfg/constants";
import { extract } from "runed";

import { getWeekDays } from "../helpers/date";

import type { CalendarDate } from "@internationalized/date";
import type { WeekStart } from "$/shared/lib/types";
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
        return startOfWeek(
            date,
            getLocale(),
            WEEK_START_PREFERENCE_MAP[extract(this.#start)]
        );
    }
}
