import { App } from "@capacitor/app";
import { Capacitor } from "@capacitor/core";
import { Dismissal } from "#shared/capabilities/dismissal/mod.svelte.js";

export async function init(): Promise<void> {
    if (!Capacitor.isNativePlatform()) {
        return;
    }

    await App.addListener("backButton", (e) => {
        if (Dismissal.requestClose()) {
            return;
        }

        if (e.canGoBack) {
            window.history.back();
        } else {
            void App.exitApp();
        }
    });
}
