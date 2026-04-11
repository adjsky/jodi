import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { Device } from "@capacitor/device";
import { page, progress, router } from "@inertiajs/svelte";
import UpsertPushSubscription from "$/generated/actions/App/Domain/Identity/Actions/UpsertPushSubscription";
import { m } from "$/paraglide/messages";
import { get } from "svelte/store";

import { PLATFORM } from "../cfg/constants";
import { destroyActionBanner } from "../ui/ActionBanner.svelte";
import { handlePushAction } from "./push-actions";
import { toaster } from "./toaster";

import type { PushActionData } from "./push-actions";

// ------------------------------ SYNCHRONIZATION ------------------------------

export const warnings = $state({
    needsConfiguration: false
});

let isSubscribing = false;

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
            if (isSubscribing) return;
            const { identifier } = await Device.getId();
            await store(token, identifier, { async: true });
        }),

        FirebaseMessaging.addListener(
            "notificationReceived",
            ({ notification }) => {
                const { title, ...options } = notification;
                if (!title) return;

                new Notification(title, options);
            }
        ),

        FirebaseMessaging.addListener(
            "notificationActionPerformed",
            (event) => {
                if (
                    typeof event.notification.data != "object" ||
                    event.notification.data == null ||
                    !("purpose" in event.notification.data)
                ) {
                    return;
                }

                handlePushAction(
                    event.notification.data as unknown as PushActionData
                );
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
        isSubscribing = true;

        const permission = await FirebaseMessaging.requestPermissions();

        if (permission.receive != "granted") {
            toaster.error(m["push-notifications.no-permission"]());
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
    } finally {
        isSubscribing = false;
    }
}

export async function unsubscribe() {
    await FirebaseMessaging.deleteToken();
}

export function ahtung(message: string) {
    // TODO: should we check permissions here? if user has a fcm token but no
    // permissions, they turned them off manually?
    const { fcm } = get(page).props.auth;
    if (fcm) return;

    toaster.info(message);
}

// ---------------------------------- HELPERS ----------------------------------

async function checkPermission() {
    const permission = await FirebaseMessaging.checkPermissions();
    return permission.receive == "granted";
}

async function getDeviceContext() {
    const { token } = await FirebaseMessaging.getToken({
        vapidKey: get(page).props.config.firebase.vapidKey
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
    await router.visit(UpsertPushSubscription(), {
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
