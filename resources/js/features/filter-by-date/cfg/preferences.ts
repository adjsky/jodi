import type { DayOfWeek } from "@internationalized/date";

export type WeekStart = "monday" | "sunday";

export const DMAP: { [D in WeekStart]: DayOfWeek } = {
    monday: "mon",
    sunday: "sun"
};
