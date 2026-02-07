import dotenv from "dotenv";

import type { CapacitorConfig } from "@capacitor/cli";

dotenv.config({ quiet: true });

const config: CapacitorConfig = {
    appId: process.env.CAPACITOR_APP_ID,
    appName: "Jodi",
    server: {
        url: process.env.CAPACITOR_SERVER_URL,
        cleartext: !process.env.CAPACITOR_SERVER_URL?.startsWith("https://")
    }
};

export default config;
