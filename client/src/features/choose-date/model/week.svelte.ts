import { startOfWeek } from "@internationalized/date";
import { WEEK_START_MAP } from "$/shared/cfg/constants";
import { getWeekDays } from "$/shared/lib/date/get-week-days";
import { extract } from "runed";

import type { CalendarDate } from "@internationalized/date";
import type { Locale } from "$/paraglide/runtime";
import type { WeekStart } from "$/shared/lib/date/types";
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

    at(offset: number): CalendarDate[] {
        return getWeekDays(
            this.#startOfWeek(extract(this.#cursor).add({ weeks: offset }))
        );
    }

    #startOfWeek(date: CalendarDate) {
        return startOfWeek(
            date,
            extract(this.#options).locale,
            WEEK_START_MAP[extract(this.#options).weekStart]
        );
    }
}
