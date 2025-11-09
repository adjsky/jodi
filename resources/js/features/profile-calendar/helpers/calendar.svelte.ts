import { getLocale } from "$/paraglide/runtime";
import dayjs from "dayjs";

import type { Dayjs } from "dayjs";

export class Calendar {
    pointer: Dayjs;

    constructor() {
        this.pointer = $state(dayjs().locale(getLocale()).startOf("isoWeek"));
    }

    get weekDays() {
        const start = this.pointer.startOf("isoWeek");
        const days = [] as Dayjs[];

        for (let i = 0; i < 7; i++) {
            const day = start.add(i, "day");
            days.push(day);
        }

        return days;
    }

    nextWeek() {
        this.pointer = this.pointer.add(1, "week").startOf("isoWeek");
    }

    previousWeek() {
        this.pointer = this.pointer.subtract(1, "week").startOf("isoWeek");
    }
}
