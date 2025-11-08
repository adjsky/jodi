import {
    baseLocale,
    defineCustomClientStrategy,
    isLocale
} from "$/paraglide/runtime";

defineCustomClientStrategy("custom-cookie", {
    getLocale() {
        const locale = document.cookie
            .split("; ")
            .find((row) => row.startsWith("jodi-locale="))
            ?.split("=")[1];

        if (!isLocale(locale)) {
            return baseLocale;
        }

        return locale;
    },
    setLocale(locale) {
        document.cookie = `jodi-locale=${locale}`;
    }
});
