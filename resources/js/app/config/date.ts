import { getLocale } from "$/paraglide/runtime";
import dayjs from "dayjs";
import isoWeek from "dayjs/plugin/isoWeek";

import type { Locale } from "$/paraglide/runtime";

dayjs.extend(isoWeek);

const locales: Record<Locale, () => Promise<unknown>> = {
    en: () => import("dayjs/locale/en"),
    ru: () => import("dayjs/locale/ru")
};

await locales[getLocale()]();
