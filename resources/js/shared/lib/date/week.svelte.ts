import { startOfWeek } from "@internationalized/date";
import { WEEK_START_MAP } from "$/shared/cfg/constants";
import { extract } from "runed";

import { getWeekDays } from "./get-week-days";

import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "$/paraglide/runtime";
import type { WeekStart } from "$/shared/lib/types";
import type { Getter, MaybeGetter } from "runed";

type Options = {
    weekStart: WeekStart;
    locale: Locale;
};

export class Week {
    #cursor: Getter<CalendarDate>;
    #options: MaybeGetter<Options>;

    constructor(cursor: Getter<CalendarDate>, options: MaybeGetter<Options>) {
        this.#cursor = cursor;
        this.#options = options;
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
            extract(this.#options).locale,
            WEEK_START_MAP[extract(this.#options).weekStart]
        );
    }
}
