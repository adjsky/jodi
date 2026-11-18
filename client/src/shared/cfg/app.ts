import { FirebaseConfigSchema } from "#shared/lib/validation/schemas.ts";
import {
    APP_DEBUG,
    APP_ENV,
    APP_URL,
    FIREBASE_VAPID_KEY,
    FIREBASE_WEB_CONFIG
} from "$app/env/public";
import z from "zod";

const AppConfigSchema = z.object({
    url: z.url(),
    firebase: z.object({
        config: FirebaseConfigSchema,
        vapidKey: z.string()
    })
});

const PLACEHOLDER = "%jodi.config.app%";

function parse_config() {
    function parse(input: unknown) {
        const result = AppConfigSchema.safeParse(input);

        if (!result.success) {
            throw new Error(
                "Failed to parse app configuration object.\n" + z.prettifyError(result.error)
            );
        }

        return result.data;
    }

    const injected_config = window.__JODI_APP_CONFIG__;

    if (injected_config === PLACEHOLDER) {
        return parse({
            url: APP_URL,
            firebase: {
                config: FIREBASE_WEB_CONFIG,
                vapidKey: FIREBASE_VAPID_KEY
            }
        });
    }

    return parse(injected_config);
}

export const app_config = {
    ...parse_config(),
    env: APP_ENV,
    debug: APP_DEBUG
};
