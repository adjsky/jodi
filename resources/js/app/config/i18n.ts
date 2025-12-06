import { page } from "@inertiajs/svelte";
import {
    baseLocale,
    defineCustomClientStrategy,
    extractLocaleFromNavigator,
    isLocale
} from "$/paraglide/runtime";
import { get } from "svelte/store";

defineCustomClientStrategy("custom-cookie", {
    getLocale() {
        const locale = document.cookie
            .split("; ")
            .find((row) => row.startsWith("jodi-locale="))
            ?.split("=")[1];

        if (!locale) {
            return extractLocaleFromNavigator();
        }

        if (!isLocale(locale)) {
            return baseLocale;
        }

        return locale;
    },
    setLocale(locale) {
        document.cookie = `jodi-locale=${locale}`;
    }
});

defineCustomClientStrategy("custom-preference", {
    getLocale() {
        const locale = get(page).props.auth.user?.preferences?.locale;

        if (!locale) {
            return;
        }

        if (!isLocale(locale)) {
            return baseLocale;
        }

        return locale;
    },
    setLocale() {}
});
