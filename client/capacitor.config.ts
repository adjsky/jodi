/// <reference types="@capacitor/local-notifications" />
/// <reference types="@capacitor/splash-screen" />

import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
    webDir: "build",
    zoomEnabled: false,
    plugins: {
        LocalNotifications: {
            smallIcon: "ic_notification"
        },
        SystemBars: {
            style: "LIGHT",
            insetsHandling: "disable"
        },
        SplashScreen: {
            launchAutoHide: false
        }
    }
};

export default config;
