import { queryOptions } from "@tanstack/svelte-query";
import ListRegistrationInvitations from "$/generated/actions/App/Domain/Identity/Actions/ListRegistrationInvitations";
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
