import ListRegistrationInvitations from "$/generated/actions/App/Domain/Identity/Actions/ListRegistrationInvitations";
import ky from "ky";

import type { RegistrationInvitationData } from "$/entities/user";
import type { ResourceFetcher } from "runed";

export const fetchInvitations: ResourceFetcher<
    unknown,
    RegistrationInvitationData[]
> = function (_, __, { signal }) {
    const { url, method } = ListRegistrationInvitations();

    return ky(url, { method, signal }).json();
};
