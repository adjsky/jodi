import * as Capacitor from "#app/init/capacitor.ts";
import * as i18n from "#app/init/i18n.ts";
import { SessionCache } from "#entities/user/model/session-cache.svelte.ts";
import { SecureStorage } from "#shared/capabilities/secure-storage/mod.ts";

import type { ClientInit } from "@sveltejs/kit";

export const init: ClientInit = async () => {
    await Promise.all([
        Capacitor.init(),
        SecureStorage.init(),
        i18n.init(),
        SessionCache.init()
    ]);
};
