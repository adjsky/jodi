import { DateFormatter } from "@internationalized/date";
import { getLocale } from "#paraglide/runtime.js";
import { TIMEZONE } from "#shared/cfg/constants.ts";

export function formatToHHMM(date: Date): string {
    return new DateFormatter(getLocale(), {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
        timeZone: TIMEZONE
    }).format(date);
}
