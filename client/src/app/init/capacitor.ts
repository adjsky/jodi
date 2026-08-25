import { App } from "@capacitor/app";
import { Capacitor } from "@capacitor/core";

export async function init(): Promise<void> {
    if (!Capacitor.isNativePlatform()) {
        return;
    }

    await App.addListener("backButton", (e) => {
        if (e.canGoBack) {
            window.history.back();
        } else {
            void App.exitApp();
        }
    });
}
