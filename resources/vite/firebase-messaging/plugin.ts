import fs from "node:fs/promises";
import path from "node:path";

import { loadEnv } from "vite";

import packageJson from "../../../package.json" with { type: "json" };

import type { MinimalPluginContextWithoutEnvironment, Plugin } from "vite";

type Context = { env: Record<string, unknown> };

const SERVICE_WORKER_FILENAME = "firebase-messaging-sw.js";
const GOOGLE_SERVICES_FILENAME = "google-services.json";

export function firebaseMessaging(): Plugin[] {
    const ctx = {} as Context;

    return [
        {
            name: "vite-plugin-firebase-messaging",
            configResolved(config) {
                ctx.env = loadEnv(config.mode, process.cwd(), "");
            }
        },
        {
            name: "vite-plugin-firebase-messaging:service-worker",
            buildStart() {
                return generateServiceWorker.call(this, ctx);
            }
        },
        {
            name: "vite-plugin-firebase-messaging:google-services",
            apply: "serve",
            buildStart() {
                return generateGoogleServices.call(this, ctx);
            }
        }
    ];
}

async function generateServiceWorker(
    this: MinimalPluginContextWithoutEnvironment,
    ctx: Context
) {
    const firebaseVersion = packageJson.devDependencies.firebase.slice(1);

    const js = `
importScripts("https://www.gstatic.com/firebasejs/${firebaseVersion}/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/${firebaseVersion}/firebase-messaging-compat.js");

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

    await fs.writeFile(
        path.join("public", SERVICE_WORKER_FILENAME),
        js.trimStart()
    );

    this.info(`${SERVICE_WORKER_FILENAME} generated`);
}

async function generateGoogleServices(
    this: MinimalPluginContextWithoutEnvironment,
    ctx: Context
) {
    const json = {
        project_info: {
            project_number: ctx.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
            project_id: ctx.env.VITE_FIREBASE_PROJECT_ID,
            storage_bucket: ctx.env.VITE_FIREBASE_STORAGE_BUCKET
        },
        client: [
            {
                client_info: {
                    mobilesdk_app_id: ctx.env.VITE_FIREBASE_APP_ID,
                    android_client_info: {
                        package_name: ctx.env.CAPACITOR_APP_ID
                    }
                },
                oauth_client: [],
                api_key: [
                    {
                        current_key: ctx.env.VITE_FIREBASE_API_KEY
                    }
                ],
                services: {
                    appinvite_service: {
                        other_platform_oauth_client: []
                    }
                }
            }
        ],
        configuration_version: "1"
    };

    await fs.writeFile(
        path.join("android", "app", GOOGLE_SERVICES_FILENAME),
        JSON.stringify(json, null, 2)
    );

    this.info(`${GOOGLE_SERVICES_FILENAME} generated`);
}
