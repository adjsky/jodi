import { extract } from "runed";

import type { Dayjs } from "dayjs";
import type { Getter } from "runed";

export class Week {
    constructor(private day: Getter<Dayjs>) {}

    get days() {
        const start = extract(this.day).startOf("isoWeek");
        const days = [] as Dayjs[];

        for (let i = 0; i < 7; i++) {
            const day = start.add(i, "day");
            days.push(day);
        }

        return days;
    }

    next() {
        return extract(this.day).add(1, "week").startOf("isoWeek");
    }

    previous() {
        return extract(this.day).subtract(1, "week").startOf("isoWeek");
    }
}
