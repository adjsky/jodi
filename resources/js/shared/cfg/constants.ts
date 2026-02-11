import { Capacitor } from "@capacitor/core";

export const LOCALE_COOKIE = "jodi-locale";
export const TIMEZONE_COOKIE = "jodi-timezone";
export const DEVICE_ID_COOKIE = "jodi-device-id";

export const TIMEZONE = Intl.DateTimeFormat().resolvedOptions().timeZone;

export const NOTIFICATION_DEFAULT_SUBHOURS = 3;

export const PLATFORM = Capacitor.getPlatform();
