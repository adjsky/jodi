import { App } from "@capacitor/app";
import { Capacitor } from "@capacitor/core";

if (Capacitor.isNativePlatform()) {
    void App.addListener("backButton", (e) => {
        if (e.canGoBack) {
            window.history.back();
        } else {
            void App.exitApp();
        }
    });
}
