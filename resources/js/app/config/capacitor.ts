import { App } from "@capacitor/app";
import { Capacitor } from "@capacitor/core";
import { Device } from "@capacitor/device";
import { DEVICE_ID_COOKIE } from "$/shared/cfg/constants";
import * as Cookie from "$/shared/lib/cookie";

if (Capacitor.isNativePlatform()) {
    await App.addListener("backButton", (e) => {
        if (e.canGoBack) {
            window.history.back();
        } else {
            void App.exitApp();
        }
    });
}

if (!Cookie.get(DEVICE_ID_COOKIE)) {
    const { identifier } = await Device.getId();

    Cookie.set(DEVICE_ID_COOKIE, identifier, {
        maxAge: 34560000,
        sameSite: "lax",
        secure: true
    });
}
