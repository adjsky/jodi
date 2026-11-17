import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { page } from "@inertiajs/svelte";
import { PLATFORM } from "#shared/cfg/capacitor.ts";
import UpsertPushSubscription from "$/generated/actions/App/Domain/Identity/Actions/UpsertPushSubscription";
import { m } from "$/paraglide/messages";
import { visit } from "$/shared/integrations/inertia/visit";
import { destroyActionBanner } from "$/shared/ui/ActionBanner.svelte";
import { toaster } from "$/shared/ui/toaster";

import { handleAction } from "./handle-action";

type StoreOptions = {
    async?: boolean;
    on_success?: VoidFunction;
    on_invalid?: VoidFunction;
};

type Warnings = { needs_configuration: boolean };

type Listener = () => Promise<void>;

export class Subscription {
    #warnings: Warnings = $state({ needs_configuration: false });
    #is_subscribing = $state(false);

    get needs_configuration(): boolean {
        const { user } = page.props.auth;

        return user?.preferences.notifications === "push" && this.#warnings.needs_configuration;
    }

    get is_subscribing(): boolean {
        return this.#is_subscribing;
    }

    async synchronize(): Promise<void> {
        const { fcm, user } = page.props.auth;
        if (!user) {
            this.#warnings.needs_configuration = false;
            return;
        }

        const has_permission = await this.#check_permission();
        if (!has_permission) {
            this.#warnings.needs_configuration = true;
            return;
        }

        this.#warnings.needs_configuration = false;

        const token = await this.#get_token();

        if (token !== fcm?.token) {
            await this.#store(token, { async: true });
        }
    }

    async listen(): Promise<Listener> {
        const handles = await Promise.all([
            FirebaseMessaging.addListener("tokenReceived", async ({ token }) => {
                if (this.#is_subscribing) return;
                await this.#store(token, { async: true });
            }),

            FirebaseMessaging.addListener("notificationReceived", ({ notification }) => {
                const { title, ...options } = notification;
                if (!title) return;

                if (typeof Notification !== "undefined" && Notification.permission === "granted") {
                    new Notification(title, options);
                } else {
                    toaster.info(title, options.body);
                }
            }),

            FirebaseMessaging.addListener("notificationActionPerformed", (event) => {
                handleAction(event.notification.data);
            })
        ]);

        return async () => {
            await Promise.all(handles.map((h) => h.remove()));
        };
    }

    async subscribe(): Promise<void> {
        if (this.#is_subscribing) return;

        try {
            this.#is_subscribing = true;

            const permission = await FirebaseMessaging.requestPermissions();

            if (permission.receive !== "granted") {
                toaster.error(m["push-notifications.no-permission"]());
                return;
            }

            const token = await this.#get_token();

            await this.#store(token, {
                on_success: () => {
                    toaster.success(m["push-notifications.success-subscribe"]());
                    destroyActionBanner("configure-push-notifications");
                    this.#warnings.needs_configuration = false;
                },
                on_invalid: () => {
                    toaster.error(m["push-notifications.failed-to-subscribe"]());
                }
            });
        } catch (e) {
            toaster.error(m["common.unexpected-error"]());
            throw e;
        } finally {
            this.#is_subscribing = false;
        }
    }

    ahtung(message: string): void {
        if (!this.needs_configuration) return;
        toaster.info(message);
    }

    async #check_permission() {
        const permission = await FirebaseMessaging.checkPermissions();
        return permission.receive === "granted";
    }

    async #get_token() {
        const { token } = await FirebaseMessaging.getToken({
            vapidKey: page.props.config.firebase.vapidKey
        });

        return token;
    }

    async #store(token: string, options?: StoreOptions) {
        await visit(UpsertPushSubscription(), {
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
                options?.on_success?.();
            },
            onHttpException() {
                options?.on_invalid?.();
                return false;
            }
        });
    }
}
