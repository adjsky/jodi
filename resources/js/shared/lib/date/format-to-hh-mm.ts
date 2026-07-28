import { DateFormatter } from "@internationalized/date";
import { getLocale } from "$/paraglide/runtime";

export function formatToHHMM(date: Date) {
    return new DateFormatter(getLocale(), {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false
    }).format(date);
}
