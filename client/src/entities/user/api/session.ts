import { api } from "#shared/api/client.ts";
import GetCurrentSession from "#wayfinder/actions/App/Domain/Identity/Actions/GetCurrentSession.ts";
import * as z from "zod";

import { PushSubscriptionSchema, UserSchema } from "../model/schemas.ts";

export function getCurrentSession(): Promise<App.Domain.Identity.Data.Output.AuthSessionData> {
    const { url, method } = GetCurrentSession();

    return api(url, { method }).json(
        z.object({
            token: z.string().nullable(),
            user: UserSchema,
            pushSubscription: PushSubscriptionSchema.nullable()
        })
    );
}
