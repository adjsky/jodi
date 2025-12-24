import { page } from "@inertiajs/svelte";
import {
    create,
    destroy
} from "$/generated/actions/App/Http/Controllers/PushSubscriptionController";
import { m } from "$/paraglide/messages";
import { fromStore } from "svelte/store";

import { urlBase64ToUint8Array } from "./buffer";
import { toaster } from "./toast";

import type { Attachment } from "svelte/attachments";

export const ensurePushSubscription: Attachment<HTMLButtonElement> = (node) => {
    const user = $derived(fromStore(page).current.props.auth.user);

    if (user.preferences.notifications != "push") {
        return;
    }

    node.addEventListener("click", ensure);

    return () => node.removeEventListener("click", ensure);
};

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
        body: JSON.stringify({ endpoint: subscription.endpoint })
    });
}

async function ensure() {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        return warn();
    }

    const pushManager = await getPushManager();

    if (!pushManager) {
        return;
    }

    let subscription = await pushManager.getSubscription();

    if (subscription) {
        return;
    }

    subscription = await pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(__VAPID_PUBLIC_KEY__)
    });

    const jsonSubscription = subscription.toJSON();

    const { url, method } = create();
    await fetch(url, {
        method,
        body: JSON.stringify({
            endpoint: jsonSubscription.endpoint,
            key: jsonSubscription.keys?.p256dh,
            token: jsonSubscription.keys?.auth
        })
    });
}

async function getPushManager(): Promise<PushManager | null> {
    if ("pushManager" in window) {
        return window.pushManager as PushManager;
    }

    const registration = await navigator.serviceWorker.getRegistration();

    return registration?.pushManager ?? null;
}

function warn() {
    toaster.info({ title: m["common.pushesAreNotSupported"]() });
}
