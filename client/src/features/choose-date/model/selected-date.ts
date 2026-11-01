import { parseDate, today } from "@internationalized/date";
import { TIMEZONE } from "#shared/cfg/constants.ts";
import { goto } from "$app/navigation";
import { page } from "$app/state";

import type { CalendarDate } from "@internationalized/date";

function update(date: CalendarDate): void {
    void goto("", {
        reset: false,
        shallow: true,
        persistState: true,
        state: {
            ...page.state,
            selectedDate: date.toString(),
            skipViewTransition: true
        }
    });
}

export const SelectedDate = {
    update,
    get current(): CalendarDate {
        return page.state.selectedDate
            ? parseDate(page.state.selectedDate)
            : today(TIMEZONE);
    }
};
