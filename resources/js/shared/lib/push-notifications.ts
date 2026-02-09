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

enum LocalStorage {
    CHECK_SUPPORT = "jodi:notifications:check-support",
    BANNER_LAST_SHOWN_AT = "jodi:notifications:banner_last_shown_at"
}

export function checkSupport() {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        return false;
    }

    return true;
}

export function checkPreference() {
    return get(page).props.auth.user.preferences.notifications == "push";
}

export async function checkSubscription() {
    const pushManager = await getPushManager();
    if (!pushManager) {
        return false;
    }

    const subscription = await pushManager.getSubscription();

    return subscription != null;
}

export async function subscribe() {
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

export async function destroySubscription() {
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

    localStorage.removeItem(LocalStorage.BANNER_LAST_SHOWN_AT);
}

export function showConfigureBannerAgain() {
    localStorage.removeItem(LocalStorage.BANNER_LAST_SHOWN_AT);
}

async function getPushManager(): Promise<PushManager | null> {
    if ("pushManager" in window) {
        return window.pushManager as PushManager;
    }

    const registration = await navigator.serviceWorker.getRegistration();

    return registration?.pushManager ?? null;
}

export function useInitBanner(redirect: () => MaybePromise) {
    onMount(async () => {
        const hasPreference = checkPreference();
        if (!hasPreference) return;

        if (!checkSupport()) {
            if (localStorage.getItem(LocalStorage.CHECK_SUPPORT) != "never") {
                createActionBanner(m["push-notifications.not-supported"](), {
                    closeable: true,
                    onDecline() {
                        localStorage.setItem(
                            LocalStorage.CHECK_SUPPORT,
                            "never"
                        );
                    }
                });
            }
            return;
        }

        const hasSubscription = await checkSubscription();
        if (hasSubscription) return;

        if (localStorage.getItem(LocalStorage.BANNER_LAST_SHOWN_AT) == null) {
            localStorage.setItem(
                LocalStorage.BANNER_LAST_SHOWN_AT,
                new Date().toISOString()
            );
            createActionBanner(m["push-notifications.configure.title"](), {
                action: m["push-notifications.configure.action"](),
                onAccept() {
                    return redirect();
                }
            });
        }
    });
}
