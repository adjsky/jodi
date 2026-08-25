import { Capacitor } from "@capacitor/core";
import { Device } from "@capacitor/device";

import packageJson from "../../../package.json" with { type: "json" };

export const VERSION = packageJson.version;

export const TIMEZONE = Intl.DateTimeFormat().resolvedOptions().timeZone;

export const NOTIFICATION_DEFAULT_SUBHOURS = 1;

export const PLATFORM = Capacitor.getPlatform();
export const DEVICE_ID = await Device.getId().then((d) => d.identifier);

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
