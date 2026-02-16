import fs from "node:fs/promises";
import path from "node:path";

import { loadEnv } from "vite";

import type { Plugin } from "vite";

type Context = { env: Record<string, unknown> };

const GOOGLE_SERVICES_FILENAME = "google-services.json";

export function googleServices(): Plugin[] {
    const ctx = {} as Context;

    return [
        {
            name: "vite-plugin-google-services",
            apply: "serve",
            configResolved(config) {
                ctx.env = loadEnv(config.mode, process.cwd(), "");
            },
            async buildStart() {
                await generateGoogleServices.call(this, ctx);
                this.info(`${GOOGLE_SERVICES_FILENAME} generated`);
            }
        }
    ];
}

export async function generateGoogleServices(ctx: Context) {
    const json = {
        project_info: {
            project_number: ctx.env.FIREBASE_MESSAGING_SENDER_ID,
            project_id: ctx.env.FIREBASE_PROJECT_ID,
            storage_bucket: ctx.env.FIREBASE_STORAGE_BUCKET
        },
        client: [
            {
                client_info: {
                    mobilesdk_app_id: ctx.env.FIREBASE_APP_ID,
                    android_client_info: {
                        package_name: ctx.env.CAPACITOR_APP_ID
                    }
                },
                oauth_client: [],
                api_key: [
                    {
                        current_key: ctx.env.FIREBASE_API_KEY
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
}
