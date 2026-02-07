import dotenv from "dotenv";

import type { CapacitorConfig } from "@capacitor/cli";

dotenv.config();

const config: CapacitorConfig = {
    appId: process.env.CAPACITOR_APP_ID,
    appName: "Jodi",
    server: {
        url: process.env.CAPACITOR_SERVER_URL,
        cleartext: true
    },
    plugins: {
        StatusBar: {
            style: "LIGHT"
        }
    }
};

export default config;
