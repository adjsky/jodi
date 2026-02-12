import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { Device } from "@capacitor/device";
import { page, progress, router } from "@inertiajs/svelte";
import {
    destroy as _destroy,
    store as _store
} from "$/generated/actions/App/Http/Controllers/PushSubscriptionController";
import { m } from "$/paraglide/messages";
import { fromStore } from "svelte/store";

import { PLATFORM } from "../cfg/constants";
import { destroyActionBanner } from "../ui/ActionBanner.svelte";
import { toaster } from "./toaster";

// ------------------------------- SYNC & STATE --------------------------------

export const synchronization = $state({
    needsConfiguration: false
});

export async function synchronize() {
    const { fcm, user } = fromStore(page).current.props.auth;
    if (user?.preferences.notifications !== "push") return;

    const hasPermission = await checkPermission();
    if (!hasPermission) {
        synchronization.needsConfiguration = true;
        return;
    }

    const deviceContext = await getDeviceContext();
    if (!deviceContext) return;

    const { token, deviceId } = deviceContext;

    if (token !== fcm?.token) {
        await store(token, deviceId, { async: true });
    }
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

        const deviceContext = await getDeviceContext();

        if (!deviceContext) {
            progress.remove();
            toaster.error(m["push-notifications.no-service-worker"]());
            return;
        }

        const { token, deviceId } = deviceContext;

        await store(token, deviceId, {
            onSuccess() {
                progress.finish();
                toaster.success(m["push-notifications.success-subscribe"]());
                destroyActionBanner("configure-push-notifications");
                synchronization.needsConfiguration = false;
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
    const hasPermission = await checkPermission();
    if (!hasPermission) return;

    if (PLATFORM == "web") {
        const reg = await navigator.serviceWorker.getRegistration();
        if (!reg) return;
    }

    await FirebaseMessaging.deleteToken();
}

// ---------------------------------- HELPERS ----------------------------------

async function checkPermission() {
    const permission = await FirebaseMessaging.checkPermissions();
    return permission.receive == "granted";
}

async function getDeviceContext() {
    let swr: ServiceWorkerRegistration | undefined;

    if (PLATFORM == "web") {
        const reg = await navigator.serviceWorker.getRegistration();
        if (!reg) return null;
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

async function store(token: string, deviceId: string, options?: StoreOptions) {
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
