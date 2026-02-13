import fs from "node:fs/promises";
import path from "node:path";

import type { MinimalPluginContextWithoutEnvironment, Plugin } from "vite";

type Context = { env: Record<string, unknown> };

const SW_FILENAME = "firebase-messaging-sw.js";

export function firebaseMessagingSw(): Plugin[] {
    const ctx = {} as Context;

    return [
        {
            name: "vite-plugin-firebase-messaging-sw:generate",
            configResolved(config) {
                ctx.env = config.env;
            },
            buildStart() {
                return generate.call(this, ctx);
            }
        }
    ];
}

async function generate(
    this: MinimalPluginContextWithoutEnvironment,
    ctx: Context
) {
    const js = `
importScripts("https://www.gstatic.com/firebasejs/12.9.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/12.9.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "${ctx.env.VITE_FIREBASE_API_KEY}",
    authDomain: "${ctx.env.VITE_FIREBASE_AUTH_DOMAIN}",
    projectId: "${ctx.env.VITE_FIREBASE_PROJECT_ID}",
    storageBucket: "${ctx.env.VITE_FIREBASE_STORAGE_BUCKET}",
    messagingSenderId: "${ctx.env.VITE_FIREBASE_MESSAGING_SENDER_ID}",
    appId: "${ctx.env.VITE_FIREBASE_APP_ID}"
});

firebase.messaging();
`;

    await fs.writeFile(path.join("public", SW_FILENAME), js.trimStart());

    this.info(`${SW_FILENAME} generated`);
}
