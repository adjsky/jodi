import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { Device } from "@capacitor/device";
import { page, progress, router } from "@inertiajs/svelte";
import {
    destroy as _destroy,
    store as _store
} from "$/generated/actions/App/Http/Controllers/PushSubscriptionController";
import { m } from "$/paraglide/messages";
import { fromStore, get } from "svelte/store";

import { PLATFORM } from "../cfg/constants";
import { destroyActionBanner } from "../ui/ActionBanner.svelte";
import { toaster } from "./toaster";

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
    const permission = await FirebaseMessaging.checkPermissions();

    if (permission.receive != "granted") {
        return;
    }

    await FirebaseMessaging.deleteToken();
}

// ------------------------------- SYNC & STATE --------------------------------

let hasSynchronized = $state(false);

export async function synchronize() {
    try {
        const permission = await FirebaseMessaging.checkPermissions();
        if (permission.receive !== "granted") return;

        const { fcm, user } = get(page).props.auth;
        if (!user) return;

        const { token, deviceId } = await getDeviceContext();

        if (token !== fcm?.token) {
            await store(token, deviceId, { async: true });
        }
    } finally {
        hasSynchronized = true;
    }
}

export function needsConfiguration() {
    if (!hasSynchronized) return false;

    const { user, fcm } = fromStore(page).current.props.auth;

    return user.preferences.notifications == "push" && fcm === null;
}

// ---------------------------------- HELPERS ----------------------------------

async function getDeviceContext() {
    let swr: ServiceWorkerRegistration | undefined;

    if (PLATFORM == "web") {
        const reg = await navigator.serviceWorker.getRegistration();
        swr = reg;
    }

    const { token } = await FirebaseMessaging.getToken({
        vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
        serviceWorkerRegistration: swr
    });

    const { identifier } = await Device.getId();

    return { token, deviceId: identifier };
}

type StoreOptions = {
    async?: boolean;
    onSuccess?: VoidFunction;
    onInvalid?: VoidFunction;
};

export async function store(
    token: string,
    deviceId: string,
    options?: StoreOptions
) {
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
            options?.onInvalid?.();
            return false;
        }
    });
}
