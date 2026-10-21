import { FirebaseConfigSchema } from "#shared/lib/validation/schemas.ts";
import {
    APP_DEBUG,
    APP_ENV,
    APP_URL,
    FIREBASE_VAPID_KEY,
    FIREBASE_WEB_CONFIG
} from "$app/env/public";
import * as z from "zod";

const AppConfigSchema = z.object({
    url: z.url(),
    firebase: z.object({
        config: FirebaseConfigSchema,
        vapidKey: z.string()
    })
});

const PLACEHOLDER = "%jodi.config.app%";

function parseConfig() {
    function parse(input: unknown) {
        const result = AppConfigSchema.safeParse(input);

        if (!result.success) {
            throw new Error(
                "Failed to parse app configuration object.\n" +
                    z.prettifyError(result.error)
            );
        }

        return result.data;
    }

    const injectedConfig = window.__JODI_APP_CONFIG__;

    if (injectedConfig === PLACEHOLDER) {
        return parse({
            url: APP_URL,
            firebase: {
                config: FIREBASE_WEB_CONFIG,
                vapidKey: FIREBASE_VAPID_KEY
            }
        });
    }

    return parse(injectedConfig);
}

export const appConfig = {
    ...parseConfig(),
    env: APP_ENV,
    debug: APP_DEBUG
};
