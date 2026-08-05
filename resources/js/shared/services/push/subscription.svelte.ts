import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { Device } from "@capacitor/device";
import { page, progress, router } from "@inertiajs/svelte";
import UpsertPushSubscription from "$/generated/actions/App/Domain/Identity/Actions/UpsertPushSubscription";
import { m } from "$/paraglide/messages";
import { PLATFORM } from "$/shared/cfg/constants";
import { destroyActionBanner } from "$/shared/ui/ActionBanner.svelte";
import { toaster } from "$/shared/ui/toaster";
import { get } from "svelte/store";

import { handleAction } from "./handle-action";

import type { PushActionData } from "./handle-action";

type StoreOptions = {
    async?: boolean;
    onSuccess?: VoidFunction;
    onInvalid?: VoidFunction;
};

type Warnings = { needsConfiguration: boolean };

type Listener = () => Promise<void>;

export class Subscription {
    #warnings: Warnings = $state({ needsConfiguration: false });
    #isSubscribing = false;

    get warnings(): Warnings {
        return this.#warnings;
    }

    async synchronize(): Promise<void> {
        const { fcm, user } = get(page).props.auth;
        if (!user) return;

        const hasPermission = await this.#checkPermission();
        if (!hasPermission) {
            this.#warnings.needsConfiguration = true;
            return;
        }

        const { token, deviceId } = await this.#getDeviceContext();

        if (token !== fcm?.token) {
            await this.#store(token, deviceId, { async: true });
        }
    }

    async listen(): Promise<Listener> {
        const handles = await Promise.all([
            FirebaseMessaging.addListener(
                "tokenReceived",
                async ({ token }) => {
                    if (this.#isSubscribing) return;
                    const { identifier } = await Device.getId();
                    await this.#store(token, identifier, { async: true });
                }
            ),

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

                    handleAction(event.notification.data as PushActionData);
                }
            )
        ]);

        return async () => {
            await Promise.all(handles.map((h) => h.remove()));
        };
    }

    async subscribe(): Promise<void> {
        try {
            this.#isSubscribing = true;

            const permission = await FirebaseMessaging.requestPermissions();

            if (permission.receive != "granted") {
                toaster.error(m["push-notifications.no-permission"]());
                return;
            }

            progress.reveal(true);
            progress.start();

            const { token, deviceId } = await this.#getDeviceContext();

            await this.#store(token, deviceId, {
                onSuccess: () => {
                    progress.finish();
                    toaster.success(
                        m["push-notifications.success-subscribe"]()
                    );
                    destroyActionBanner("configure-push-notifications");
                    this.#warnings.needsConfiguration = false;
                },
                onInvalid: () => {
                    progress.remove();
                    toaster.error(
                        m["push-notifications.failed-to-subscribe"]()
                    );
                }
            });
        } catch (e) {
            progress.remove();
            console.error(e);
            toaster.error(m["common.unexpected-error"]());
        } finally {
            this.#isSubscribing = false;
        }
    }

    async unsubscribe(): Promise<void> {
        await FirebaseMessaging.deleteToken();
    }

    ahtung(message: string): void {
        // TODO: should we check permissions here? if user has a fcm token but no
        // permissions, they turned them off manually?
        const { fcm } = get(page).props.auth;
        if (fcm) return;

        toaster.info(message);
    }

    async #checkPermission() {
        const permission = await FirebaseMessaging.checkPermissions();
        return permission.receive == "granted";
    }

    async #getDeviceContext() {
        const { token } = await FirebaseMessaging.getToken({
            vapidKey: get(page).props.config.firebase.vapidKey
        });

        const { identifier } = await Device.getId();

        return { token, deviceId: identifier };
    }

    async #store(token: string, deviceId: string, options?: StoreOptions) {
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
}
