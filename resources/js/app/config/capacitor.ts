import { App } from "@capacitor/app";
import { Capacitor } from "@capacitor/core";

if (Capacitor.isNativePlatform()) {
    await App.addListener("backButton", (e) => {
        if (e.canGoBack) {
            window.history.back();
        } else {
            void App.exitApp();
        }
    });
}
