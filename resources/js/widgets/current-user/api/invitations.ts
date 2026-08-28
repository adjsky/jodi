import { mutationOptions, queryOptions } from "@tanstack/svelte-query";
import DestroyRegistrationInvitation from "$/generated/actions/App/Domain/Identity/Actions/DestroyRegistrationInvitation";
import ListRegistrationInvitations from "$/generated/actions/App/Domain/Identity/Actions/ListRegistrationInvitations";
import { m } from "$/paraglide/messages";
import { toaster } from "$/shared/ui/toaster";
import ky from "ky";

import type { RegistrationInvitationData } from "$/entities/user";

export const invitationsQueryOptions = queryOptions<
    RegistrationInvitationData[]
>({
    queryKey: ["invitations"],
    queryFn({ signal }) {
        const { url, method } = ListRegistrationInvitations();

        return ky(url, { method, signal }).json();
    }
});

export const deleteInvitationMutationOptions = mutationOptions({
    mutationFn(id: string) {
        const { url, method } = DestroyRegistrationInvitation(id);

        return ky(url, { method });
    },
    async onMutate(id, context) {
        await context.client.cancelQueries({
            queryKey: invitationsQueryOptions.queryKey
        });

        const previousInvitations = context.client.getQueryData(
            invitationsQueryOptions.queryKey
        );

        context.client.setQueryData(invitationsQueryOptions.queryKey, (prev) =>
            prev?.filter((invitation) => invitation.id != id)
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
