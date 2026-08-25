import * as z from "zod";

export const FirebaseConfigSchema = z.any().pipe(
    z.preprocess(
        (input, ctx) => {
            if (typeof input === "string") {
                try {
                    return JSON.parse(input);
                } catch {
                    ctx.issues.push({
                        code: "custom",
                        message: "Invalid JSON",
                        input
                    });

                    return z.NEVER;
                }
            }

            return input;
        },
        z.object({
            apiKey: z.string(),
            authDomain: z.string(),
            projectId: z.string(),
            storageBucket: z.string(),
            messagingSenderId: z.string(),
            appId: z.string()
        })
    )
);
