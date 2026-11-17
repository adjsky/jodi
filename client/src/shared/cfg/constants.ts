import packageJson from "../../../package.json" with { type: "json" };

export const VERSION = packageJson.version;

export const TIMEZONE = Intl.DateTimeFormat().resolvedOptions().timeZone;
export const UTC = "UTC";

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
