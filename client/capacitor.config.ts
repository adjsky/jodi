/// <reference types="@capacitor/splash-screen" />

import { KeyboardResize } from "@capacitor/keyboard";

import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
    webDir: "build",
    zoomEnabled: false,
    plugins: {
        SystemBars: {
            style: "LIGHT",
            insetsHandling: "native"
        },
        SplashScreen: {
            launchAutoHide: false
        },
        Keyboard: {
            resize: KeyboardResize.None
        }
    }
};

export default config;
