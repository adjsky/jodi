import { page } from "@inertiajs/svelte";
import {
    baseLocale,
    defineCustomClientStrategy,
    extractLocaleFromNavigator,
    isLocale
} from "$/paraglide/runtime";
import { LOCALE_COOKIE } from "$/shared/cfg/constants";
import Cookies from "js-cookie";
import { get } from "svelte/store";

defineCustomClientStrategy("custom-cookie", {
    getLocale() {
        const locale = Cookies.get(LOCALE_COOKIE);

        if (!locale) {
            return extractLocaleFromNavigator();
        }

        if (!isLocale(locale)) {
            return baseLocale;
        }

        return locale;
    },
    setLocale(locale) {
        Cookies.set(LOCALE_COOKIE, locale, {
            sameSite: "lax",
            expires: 365
        });
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
