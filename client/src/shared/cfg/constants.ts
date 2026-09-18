import { Capacitor } from "@capacitor/core";

export const TIMEZONE_HEADER = "jodi-timezone";
export const DEVICE_ID_HEADER = "jodi-device-id";

export const TIMEZONE = Intl.DateTimeFormat().resolvedOptions().timeZone;

export const NOTIFICATION_DEFAULT_SUBHOURS = 1;

export const PLATFORM = Capacitor.getPlatform();

export const LANGUAGES = {
    en: "English",
    ru: "Русский"
} as const;

export const WEEK_START_MAP = {
    monday: "mon",
    sunday: "sun"
} as const;

export enum DEFER_FRAMES {
    SHEET = 1
}

export const TOUCH_SLOP = 10;
