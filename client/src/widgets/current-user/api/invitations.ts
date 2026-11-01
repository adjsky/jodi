import { mutationOptions, queryOptions } from "@tanstack/svelte-query";
import { m } from "#paraglide/messages.js";
import { api } from "#shared/api/client.ts";
import { toaster } from "#shared/ui/toaster.ts";
import DestroyRegistrationInvitation from "#wayfinder/actions/App/Domain/Identity/Actions/DestroyRegistrationInvitation.ts";
import ListRegistrationInvitations from "#wayfinder/actions/App/Domain/Identity/Actions/ListRegistrationInvitations.ts";
import z from "zod";

import type { RegistrationInvitationData } from "#entities/user/mod.ts";

export const invitationsQueryOptions = queryOptions<
    RegistrationInvitationData[]
>({
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

export const deleteInvitationMutationOptions = mutationOptions({
    mutationKey: ["invitations", "delete"],
    mutationFn(id: string) {
        const { url, method } = DestroyRegistrationInvitation(id);
        return api(url, { method });
    },
    async onMutate(id, context) {
        await context.client.cancelQueries({
            queryKey: invitationsQueryOptions.queryKey
        });

        const previousInvitations = context.client.getQueryData(
            invitationsQueryOptions.queryKey
        );

        context.client.setQueryData(invitationsQueryOptions.queryKey, (prev) =>
            prev?.filter((invitation) => invitation.id !== id)
        );

        return { previousInvitations };
    },
    onError(_, __, result, context) {
        context.client.setQueryData(
            invitationsQueryOptions.queryKey,
            result?.previousInvitations
        );

        toaster.error(m["current-user.invitations.errors.delete"]());
    },
    onSettled(_, __, ___, ____, context) {
        return context.client.invalidateQueries({
            queryKey: invitationsQueryOptions.queryKey
        });
    }
});
