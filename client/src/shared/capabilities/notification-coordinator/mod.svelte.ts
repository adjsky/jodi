import { App } from "@capacitor/app";
import { LocalNotifications } from "@capacitor/local-notifications";
import { SessionCache } from "#entities/user/mod.ts";
import { api } from "#shared/api/client.ts";
import { app_config } from "#shared/cfg/app.ts";
import { PLATFORM } from "#shared/cfg/capacitor.ts";
import UpdateUserPreferences from "#wayfinder/actions/App/Domain/Identity/Actions/UpdateUserPreferences.ts";
import { initializeApp } from "firebase/app";

import { Push } from "../push/mod.ts";

import type { PermissionState } from "@capacitor/core";

let permission: PermissionState | null = $state(null);

async function init(): Promise<void> {
    if (PLATFORM === "web") {
        initializeApp(app_config.firebase.config);
    }

    await Push.subscription.listen();

    await App.addListener("appStateChange", ({ isActive }) => {
        if (isActive) {
            void refresh_permission();
        }
    });

    if (PLATFORM === "web" && navigator.permissions) {
        const status = await navigator.permissions
            .query({ name: "notifications" })
            .catch(() => null);

        status?.addEventListener("change", () => {
            void refresh_permission();
        });
    }

    await refresh_permission();
}

async function refresh_permission(): Promise<void> {
    const result = await LocalNotifications.checkPermissions();
    permission = result.display;

    if (permission === "granted") {
        void Push.subscription.synchronize();
    }
}

async function enable(fallback_mail = false): Promise<void> {
    const result = await LocalNotifications.requestPermissions();
    permission = result.display;

    if (permission !== "granted" && fallback_mail) {
        const { url, method } = UpdateUserPreferences();
        void api(url, { method, json: { notifications: "mail" } }).then(() =>
            SessionCache.update({ "user.preferences.notifications": "mail" })
        );
    }

    if (permission === "granted") {
        void Push.subscription.synchronize();
    }
}

export const NotificationCoordinator = {
    init,
    enable,
    get permission(): PermissionState {
        if (permission === null) {
            throw new Error(
                "NotificationCoordinator.init should be called before using permission."
            );
        }

        return permission;
    }
};
