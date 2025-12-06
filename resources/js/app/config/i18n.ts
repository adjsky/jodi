import { page } from "@inertiajs/svelte";
import {
    baseLocale,
    defineCustomClientStrategy,
    extractLocaleFromNavigator,
    isLocale
} from "$/paraglide/runtime";
import * as Cookie from "$/shared/lib/cookie";
import { get } from "svelte/store";

defineCustomClientStrategy("custom-cookie", {
    getLocale() {
        const locale = Cookie.get("jodi-locale");

        if (!locale) {
            return extractLocaleFromNavigator();
        }

        if (!isLocale(locale)) {
            return baseLocale;
        }

        return locale;
    },
    setLocale(locale) {
        Cookie.set("jodi-locale", locale, {
            maxAge: 34560000,
            sameSite: "lax"
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
