/// <reference types="@capacitor/splash-screen" />

import { KeyboardResize } from "@capacitor/keyboard";
import dotenv from "dotenv";

import type { CapacitorConfig } from "@capacitor/cli";

dotenv.config({ quiet: true });

const config: CapacitorConfig = {
    appId: process.env.CAPACITOR_APP_ID,
    appName: process.env.APP_NAME,
    webDir: "resources/capacitor",
    server: {
        url: process.env.CAPACITOR_SERVER_URL,
        cleartext: !process.env.CAPACITOR_SERVER_URL?.startsWith("https://")
    },
    appendUserAgent: "AppId=Jodi",
    plugins: {
        SystemBars: {
            style: "LIGHT",
            insetsHandling: "disable"
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
