import { defineEnvVars } from "@sveltejs/kit/env";
import { FirebaseConfigSchema } from "#shared/lib/validation/schemas.ts";
import z from "zod";

export const variables = defineEnvVars({
    APP_URL: {
        public: true,
        schema: z.url()
    },
    APP_ENV: {
        public: true,
        schema: z.enum(["local", "productiion"])
    },
    APP_DEBUG: {
        public: true,
        schema: z.stringbool()
    },
    FIREBASE_WEB_CONFIG: {
        public: true,
        schema: FirebaseConfigSchema
    },
    FIREBASE_VAPID_KEY: {
        public: true,
        schema: z.string()
    }
});
