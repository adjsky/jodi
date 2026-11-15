import { TIMEZONE } from "#shared/cfg/constants.ts";

import type { RecurrenceException } from "./types.ts";
import type { CalendarDate, ZonedDateTime } from "@internationalized/date";

type DateKey<T> = {
    [K in keyof T]-?: NonNullable<T[K]> extends ZonedDateTime ? K : never;
}[keyof T];

type RecurrenceConfig<T> = {
    startKey: DateKey<T>;
    endKey: DateKey<T> | null;
    shiftedDateKeys: DateKey<T>[];
};

export class RecurrenceExpander<T> {
    #config: RecurrenceConfig<T>;

    constructor(config: RecurrenceConfig<T>) {
        this.#config = config;
    }

    on(date: CalendarDate, items: T[], exceptions: RecurrenceException[]): T[] {
        const start = date.toDate(TIMEZONE).getTime();
        const end = date.add({ days: 1 }).toDate(TIMEZONE).getTime();
    }
}
