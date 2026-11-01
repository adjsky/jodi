import { mutationOptions } from "@tanstack/svelte-query";
import { SessionCache } from "#entities/user/mod.ts";
import { m } from "#paraglide/messages.js";
import { api } from "#shared/api/client.ts";
import { toaster } from "#shared/ui/toaster.ts";
import UpdateUserPreferences from "#wayfinder/actions/App/Domain/Identity/Actions/UpdateUserPreferences.ts";

export const updateUserPreferencesMutationOptions = mutationOptions({
    mutationKey: ["preferences", "update"],
    mutationFn(
        preferences: App.Domain.Identity.Data.Input.UpdateUserPreferencesData
    ) {
        const { url, method } = UpdateUserPreferences();
        return api(url, { method, json: preferences });
    },
    async onMutate(changes) {
        const { user } = SessionCache.required;

        const previous = { ...user.preferences };

        await SessionCache.update({
            "user.preferences": { ...previous, ...changes }
        });

        return { userId: user.id, previous };
    },
    async onError(_, __, result) {
        if (!result || SessionCache.current?.user.id !== result.userId) {
            return;
        }

        toaster.error(m["current-user.preferences.errors.update"]());

        await SessionCache.update({ "user.preferences": result.previous });
    }
});
