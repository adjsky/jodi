import { SessionCache } from "#entities/user/mod.ts";
import {
    baseLocale,
    defineCustomClientStrategy,
    isLocale
} from "#paraglide/runtime.js";

export function init(): void {
    defineCustomClientStrategy("custom-preference", {
        getLocale() {
            const locale = SessionCache.current?.user.preferences.locale;
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
}
