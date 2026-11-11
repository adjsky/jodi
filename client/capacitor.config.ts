/// <reference types="@capacitor/splash-screen" />

import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
    webDir: "build",
    zoomEnabled: false,
    plugins: {
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
