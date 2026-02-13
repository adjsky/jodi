import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { Device } from "@capacitor/device";
import { page, progress, router } from "@inertiajs/svelte";
import {
    destroy as _destroy,
    store as _store
} from "$/generated/actions/App/Http/Controllers/PushSubscriptionController";
import { m } from "$/paraglide/messages";
import { get } from "svelte/store";

import { PLATFORM } from "../cfg/constants";
import { destroyActionBanner } from "../ui/ActionBanner.svelte";
import { toaster } from "./toaster";

// ------------------------------ SYNCHRONIZATION ------------------------------

export const warnings = $state({
    needsConfiguration: false
});

export async function synchronize() {
    const { fcm, user } = get(page).props.auth;
    if (!user) return;

    const hasPermission = await checkPermission();
    if (!hasPermission) {
        warnings.needsConfiguration = true;
        return;
    }

    const { token, deviceId } = await getDeviceContext();

    if (token !== fcm?.token) {
        await store(token, deviceId, { async: true });
    }
}

export async function setupListeners() {
    const [tokenHandle, notificationHandle] = await Promise.all([
        FirebaseMessaging.addListener("tokenReceived", async ({ token }) => {
            const { identifier } = await Device.getId();
            await store(token, identifier, { async: true });
        }),
        FirebaseMessaging.addListener(
            "notificationReceived",
            ({ notification }) => {
                const { title } = notification;
                if (!title) return;

                toaster.info(title);
            }
        )
    ]);

    return async () => {
        await Promise.all([tokenHandle.remove(), notificationHandle.remove()]);
    };
}

// ---------------------------------- ACTIONS ----------------------------------

export async function subscribe() {
    try {
        const permission = await FirebaseMessaging.requestPermissions();

        if (permission.receive != "granted") {
            toaster.error(m["push-notifications.failed-to-subscribe"]());
            return;
        }

        progress.reveal(true);
        progress.start();

        const { token, deviceId } = await getDeviceContext();

        await store(token, deviceId, {
            onSuccess() {
                progress.finish();
                toaster.success(m["push-notifications.success-subscribe"]());
                destroyActionBanner("configure-push-notifications");
                warnings.needsConfiguration = false;
            },
            onInvalid() {
                progress.remove();
                toaster.error(m["push-notifications.failed-to-subscribe"]());
            }
        });
    } catch (e) {
        progress.remove();
        console.error(e);
        toaster.error(m["common.unexpected-error"]());
    }
}

export async function unsubscribe() {
    await FirebaseMessaging.deleteToken();
}

// ---------------------------------- HELPERS ----------------------------------

async function checkPermission() {
    const permission = await FirebaseMessaging.checkPermissions();
    return permission.receive == "granted";
}

async function getDeviceContext() {
    const { token } = await FirebaseMessaging.getToken({
        vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY
    });

    const { identifier } = await Device.getId();

    return { token, deviceId: identifier };
}

type StoreOptions = {
    async?: boolean;
    onSuccess?: VoidFunction;
    onInvalid?: VoidFunction;
};

let lastStoredToken: string | null = null;

async function store(token: string, deviceId: string, options?: StoreOptions) {
    // This is the easiest approach to prevent multiple calls to backend when
    // we receive two identical tokens because the `tokenReceived` event fires
    // even when we manually call `getToken`.
    if (lastStoredToken === token) return;
    const previousLastStoredToken = lastStoredToken;
    lastStoredToken = token;

    await router.visit(_store(), {
        data: {
            fcm_token: token,
            platform: PLATFORM,
            device_id: deviceId
        },
        async: options?.async,
        showProgress: false,
        replace: true,
        preserveUrl: true,
        preserveState: true,
        preserveScroll: true,
        only: ["auth"],
        onSuccess() {
            options?.onSuccess?.();
        },
        onInvalid() {
            lastStoredToken = previousLastStoredToken;
            options?.onInvalid?.();
            return false;
        }
    });
}
