import { page } from "@inertiajs/svelte";
import {
    create,
    destroy
} from "$/generated/actions/App/Http/Controllers/PushSubscriptionController";
import { m } from "$/paraglide/messages";
import { onMount } from "svelte";
import { get } from "svelte/store";

import { createActionBanner } from "../ui/ActionBanner.svelte";
import { urlBase64ToUint8Array } from "./buffer";

import type { MaybePromise } from "./types";

const CHECK_SUPPORT_LS_KEY = "jodi:notifications:check-support";
const CONFIGURE_LS_KEY = "jodi:notifications:banner_last_opened_at";

export function checkPushNotificationsSupport() {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        return false;
    }

    return true;
}

export function checkPushNotificationPreference() {
    return get(page).props.auth.user.preferences.notifications == "push";
}

export async function checkHasPushNotificationsSubscription() {
    const pushManager = await getPushManager();
    if (!pushManager) {
        return false;
    }

    const subscription = await pushManager.getSubscription();

    return subscription != null;
}

export async function subscribeToPushNotifications() {
    const pushManager = await getPushManager();
    if (!pushManager) return;

    let subscription = await pushManager.getSubscription();
    if (subscription) return;

    subscription = await pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(
            get(page).props.config.VAPID_PUBLIC_KEY
        )
    });

    const jsonSubscription = subscription.toJSON();

    const { url, method } = create();

    await fetch(url, {
        method,
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            endpoint: jsonSubscription.endpoint,
            key: jsonSubscription.keys?.p256dh,
            token: jsonSubscription.keys?.auth
        })
    });
}

export async function destroyPushSubscription() {
    const pushManager = await getPushManager();

    if (!pushManager) {
        return;
    }

    const subscription = await pushManager.getSubscription();

    if (!subscription) {
        return;
    }

    await subscription.unsubscribe();

    const { url, method } = destroy();
    await fetch(url, {
        method,
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ endpoint: subscription.endpoint })
    });

    localStorage.removeItem(CONFIGURE_LS_KEY);
}

export function showConfigurePushBannerAgain() {
    localStorage.removeItem(CONFIGURE_LS_KEY);
}

async function getPushManager(): Promise<PushManager | null> {
    if ("pushManager" in window) {
        return window.pushManager as PushManager;
    }

    const registration = await navigator.serviceWorker.getRegistration();

    return registration?.pushManager ?? null;
}

export function useNotificationsInitBanner(redirect: () => MaybePromise) {
    onMount(async () => {
        const hasPreference = await checkPushNotificationPreference();
        if (!hasPreference) return;

        if (
            localStorage.getItem(CHECK_SUPPORT_LS_KEY) != "never" &&
            !checkPushNotificationsSupport()
        ) {
            createActionBanner(m["push-notifications.not-supported"](), {
                closeable: true,
                onDecline() {
                    localStorage.setItem(CHECK_SUPPORT_LS_KEY, "never");
                }
            });
            return;
        }

        if (localStorage.getItem(CONFIGURE_LS_KEY) == null) {
            localStorage.setItem(CONFIGURE_LS_KEY, new Date().toISOString());
            createActionBanner(m["push-notifications.configure.title"](), {
                action: m["push-notifications.configure.action"](),
                onAccept() {
                    return redirect();
                }
            });
        }
    });
}
