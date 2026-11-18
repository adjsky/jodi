import * as Capacitor from "#app/init/capacitor.ts";
import * as i18n from "#app/init/i18n.ts";
import { SessionCache } from "#entities/user/model/session-cache.svelte.ts";
import { NotificationCoordinator } from "#shared/capabilities/notification-coordinator/mod.svelte.ts";
import { SecureStorage } from "#shared/capabilities/secure-storage/mod.ts";

import type { ClientInit } from "@sveltejs/kit";

export const init: ClientInit = async () => {
    await Promise.all([SecureStorage.init(), SessionCache.init(), Capacitor.init()]);
    await NotificationCoordinator.init();
    i18n.init();
};
