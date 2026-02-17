import dotenv from "dotenv";

import type { CapacitorConfig } from "@capacitor/cli";

dotenv.config({ quiet: true });

const config: CapacitorConfig = {
    appId: process.env.CAPACITOR_APP_ID,
    appName: "Jodi",
    webDir: "resources/capacitor",
    server: {
        url: process.env.CAPACITOR_SERVER_URL,
        cleartext: !process.env.CAPACITOR_SERVER_URL?.startsWith("https://")
    },
    plugins: {
        SystemBars: {
            style: "LIGHT"
        }
    }
};

export default config;
