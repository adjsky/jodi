import { Capacitor } from "@capacitor/core";

import type { WeekStart } from "../lib/types";
import type { DayOfWeek } from "@internationalized/date";

export const LOCALE_COOKIE = "jodi-locale";
export const TIMEZONE_COOKIE = "jodi-timezone";
export const DEVICE_ID_COOKIE = "jodi-device-id";

export const TIMEZONE = Intl.DateTimeFormat().resolvedOptions().timeZone;

export const NOTIFICATION_DEFAULT_SUBHOURS = 3;

export const PLATFORM = Capacitor.getPlatform();

export const WEEK_START_PREFERENCE_MAP: { [D in WeekStart]: DayOfWeek } = {
    monday: "mon",
    sunday: "sun"
};

export const WEEK_CAROUSEL_CACHE_TAG = "week-carousel";
