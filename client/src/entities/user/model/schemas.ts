import * as z from "zod";

export const UserSchema = z.object({
    id: z.number(),
    name: z.string(),
    email: z.string(),
    preferences: z.object({
        locale: z.string(),
        timezone: z.string(),
        weekStartOn: z.enum(["sunday", "monday"]),
        notifications: z.enum(["push", "mail"])
    })
});

export const PushSubscriptionSchema = z.object({
    deviceId: z.string(),
    fcmToken: z.string()
});
