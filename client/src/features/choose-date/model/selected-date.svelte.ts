import { today } from "@internationalized/date";
import { TIMEZONE } from "#shared/cfg/constants.ts";

import type { CalendarDate } from "@internationalized/date";

let selected = $state(today(TIMEZONE));

export const SelectedDate = {
    update(date: CalendarDate): void {
        selected = date;
    },
    get current(): CalendarDate {
        return selected;
    }
};
