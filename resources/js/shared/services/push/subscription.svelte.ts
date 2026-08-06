import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { page, progress, router } from "@inertiajs/svelte";
import UpsertPushSubscription from "$/generated/actions/App/Domain/Identity/Actions/UpsertPushSubscription";
import { m } from "$/paraglide/messages";
import { PLATFORM } from "$/shared/cfg/constants";
import { destroyActionBanner } from "$/shared/ui/ActionBanner.svelte";
import { toaster } from "$/shared/ui/toaster";
import { get } from "svelte/store";

import { handleAction } from "./handle-action";

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

    get needsConfiguration(): boolean {
        const { user } = get(page).props.auth;

        return (
            user?.preferences.notifications == "push" &&
            this.#warnings.needsConfiguration
        );
    }

    async synchronize(): Promise<void> {
        const { fcm, user } = get(page).props.auth;
        if (!user) {
            this.#warnings.needsConfiguration = false;
            return;
        }

        const hasPermission = await this.#checkPermission();
        if (!hasPermission) {
            this.#warnings.needsConfiguration = true;
            return;
        }

        this.#warnings.needsConfiguration = false;

        const token = await this.#getToken();

        if (token !== fcm?.token) {
            await this.#store(token, { async: true });
        }
    }

    async listen(): Promise<Listener> {
        const handles = await Promise.all([
            FirebaseMessaging.addListener(
                "tokenReceived",
                async ({ token }) => {
                    if (this.#isSubscribing) return;
                    await this.#store(token, { async: true });
                }
            ),

            FirebaseMessaging.addListener(
                "notificationReceived",
                ({ notification }) => {
                    const { title, ...options } = notification;
                    if (!title) return;

                    if (
                        typeof Notification != "undefined" &&
                        Notification.permission == "granted"
                    ) {
                        new Notification(title, options);
                    } else {
                        toaster.info(title, options.body);
                    }
                }
            ),

            FirebaseMessaging.addListener(
                "notificationActionPerformed",
                (event) => {
                    handleAction(event.notification.data);
                }
            )
        ]);

        return async () => {
            await Promise.all(handles.map((h) => h.remove()));
        };
    }

    async subscribe(): Promise<void> {
        if (this.#isSubscribing) return;

        try {
            this.#isSubscribing = true;

            const permission = await FirebaseMessaging.requestPermissions();

            if (permission.receive != "granted") {
                toaster.error(m["push-notifications.no-permission"]());
                return;
            }

            progress.reveal(true);
            progress.start();

            const token = await this.#getToken();

            await this.#store(token, {
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
            toaster.error(m["common.unexpected-error"]());
            throw e;
        } finally {
            this.#isSubscribing = false;
        }
    }

    ahtung(message: string): void {
        if (!this.needsConfiguration) return;
        toaster.info(message);
    }

    async #checkPermission() {
        const permission = await FirebaseMessaging.checkPermissions();
        return permission.receive == "granted";
    }

    async #getToken() {
        const { token } = await FirebaseMessaging.getToken({
            vapidKey: get(page).props.config.firebase.vapidKey
        });

        return token;
    }

    async #store(token: string, options?: StoreOptions) {
        await router.visit(UpsertPushSubscription(), {
            data: {
                fcmToken: token,
                platform: PLATFORM
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
