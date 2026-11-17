import { mutationOptions } from "@tanstack/svelte-query";
import { SessionCache } from "#entities/user/mod.ts";
import { m } from "#paraglide/messages.js";
import { api } from "#shared/api/client.ts";
import { toaster } from "#shared/ui/toaster.ts";
import UpdateUserPreferences from "#wayfinder/actions/App/Domain/Identity/Actions/UpdateUserPreferences.ts";

export const update_user_preferences_mutation_options = mutationOptions({
    mutationKey: ["preferences", "update"],
    mutationFn(preferences: App.Domain.Identity.Data.Input.UpdateUserPreferencesData) {
        const { url, method } = UpdateUserPreferences();
        return api(url, { method, json: preferences });
    },
    async onMutate(changes) {
        const { user } = SessionCache.required;

        const previous = { ...user.preferences };

        await SessionCache.update({
            "user.preferences": { ...previous, ...changes }
        });

        return { user_id: user.id, previous };
    },
    async onError(_error, _preferences, result) {
        if (!result || SessionCache.current?.user.id !== result.user_id) {
            return;
        }

        toaster.error(m["current-user.preferences.errors.update"]());

        await SessionCache.update({ "user.preferences": result.previous });
    }
});
