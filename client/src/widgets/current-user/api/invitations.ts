import { mutationOptions, queryOptions } from "@tanstack/svelte-query";
import { m } from "#paraglide/messages.js";
import { api } from "#shared/api/client.ts";
import { toaster } from "#shared/ui/toaster.ts";
import DestroyRegistrationInvitation from "#wayfinder/actions/App/Domain/Identity/Actions/DestroyRegistrationInvitation.ts";
import ListRegistrationInvitations from "#wayfinder/actions/App/Domain/Identity/Actions/ListRegistrationInvitations.ts";
import z from "zod";

import type { RegistrationInvitationData } from "#entities/user/mod.ts";

export const invitations_query_options = queryOptions<RegistrationInvitationData[]>({
    queryKey: ["invitations"],
    queryFn({ signal }) {
        const { url, method } = ListRegistrationInvitations();
        return api(url, { method, signal }).json(
            z.array(
                z.object({
                    id: z.string(),
                    email: z.string(),
                    expiresAt: z.iso.datetime({ offset: true }),
                    shareUrl: z.string()
                })
            )
        );
    }
});

export const delete_invitation_mutation_options = mutationOptions({
    mutationKey: ["invitations", "delete"],
    mutationFn(id: string) {
        const { url, method } = DestroyRegistrationInvitation(id);
        return api(url, { method });
    },
    async onMutate(id, context) {
        await context.client.cancelQueries({
            queryKey: invitations_query_options.queryKey
        });

        const previous_invitations = context.client.getQueryData(
            invitations_query_options.queryKey
        );

        context.client.setQueryData(invitations_query_options.queryKey, (prev) =>
            prev?.filter((invitation) => invitation.id !== id)
        );

        return { previous_invitations };
    },
    onError(_error, _id, result, context) {
        context.client.setQueryData(
            invitations_query_options.queryKey,
            result?.previous_invitations
        );

        toaster.error(m["current-user.invitations.errors.delete"]());
    },
    onSettled(_data, _error, _id, _result, context) {
        return context.client.invalidateQueries({
            queryKey: invitations_query_options.queryKey
        });
    }
});
