import * as Cookie from "$/shared/lib/cookie";

// TODO: remove when we can load locales lazily
import "dayjs/locale/en";
import "dayjs/locale/ru";

import { TIMEZONE_COOKIE } from "$/shared/cfg/constants";
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";

dayjs.extend(utc);

// TODO: find out how to lazily load dayjs locales
//
// const locales: Record<Locale, () => Promise<unknown>> = {
//     en: () => import("dayjs/locale/en"),
//     ru: () => import("dayjs/locale/ru")
// };
//
// await locales[getLocale()]();

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

if (Cookie.get(TIMEZONE_COOKIE) != timezone) {
    Cookie.set(TIMEZONE_COOKIE, timezone, {
        maxAge: 34560000,
        sameSite: "lax"
    });
}
