import { FirebaseMessaging } from "@capacitor-firebase/messaging";
import { SessionCache } from "#entities/user/mod.ts";
import { api } from "#shared/api/client.ts";
import { app_config } from "#shared/cfg/app.ts";
import { DEVICE_ID, PLATFORM } from "#shared/cfg/capacitor.ts";
import { toaster } from "#shared/ui/toaster.ts";
import UpsertPushSubscription from "#wayfinder/actions/App/Domain/Identity/Actions/UpsertPushSubscription.ts";

type Listener = () => Promise<void>;

export class Subscription {
    #synchronization: Promise<void> | null = null;

    synchronize(): Promise<void> {
        this.#synchronization ??= this.#synchronize().finally(() => {
            this.#synchronization = null;
        });

        return this.#synchronization;
    }

    async #synchronize(): Promise<void> {
        const session = SessionCache.current;
        if (!session) return;

        const has_permission = await this.#check_permission();
        if (!has_permission) return;

        const token = await this.#get_token();

        if (token !== session.pushSubscription?.fcmToken) {
            await this.#store(token);
        }
    }

    async listen(): Promise<Listener> {
        const handles = await Promise.all([
            FirebaseMessaging.addListener("tokenReceived", async ({ token }) => {
                await this.#store(token);
            }),

            FirebaseMessaging.addListener("notificationReceived", ({ notification }) => {
                const { title, ...options } = notification;
                if (!title) return;
                toaster.info(title, { description: options.body });
            })
        ]);

        return async () => {
            await Promise.all(handles.map((h) => h.remove()));
        };
    }

    async #check_permission() {
        const permission = await FirebaseMessaging.checkPermissions();
        return permission.receive === "granted";
    }

    async #get_token() {
        const { token } = await FirebaseMessaging.getToken({
            vapidKey: app_config.firebase.vapidKey
        });

        return token;
    }

    async #store(fcm_token: string) {
        const session = SessionCache.current;
        if (!session) return;

        const { url, method } = UpsertPushSubscription();

        try {
            await api(url, { method, json: { fcmToken: fcm_token, platform: PLATFORM } });

            if (session.user.id === SessionCache.current?.user.id) {
                await SessionCache.update({
                    pushSubscription: { deviceId: DEVICE_ID, fcmToken: fcm_token }
                });
            }
        } catch {
            //
        }
    }
}
