import dayjs from "dayjs";
import isoWeek from "dayjs/plugin/isoWeek";

// TODO: remove when we can load locales lazily
import "dayjs/locale/en";
import "dayjs/locale/ru";

dayjs.extend(isoWeek);

// TODO: find out how to lazily load dayjs locales
//
// const locales: Record<Locale, () => Promise<unknown>> = {
//     en: () => import("dayjs/locale/en"),
//     ru: () => import("dayjs/locale/ru")
// };
//
// await locales[getLocale()]();
