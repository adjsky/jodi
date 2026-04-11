import GetRegistrationInvitation from "$/generated/actions/App/Domain/Identity/Actions/GetRegistrationInvitation";
import ListRegistrationInvitations from "$/generated/actions/App/Domain/Identity/Actions/ListRegistrationInvitations";

import type { RegistrationInvitationData } from "$/entities/user/model/types";
import type { ResourceFetcher } from "runed";

export class NotFoundResourceError extends Error {}

export const fetchInvitations: ResourceFetcher<
    unknown,
    RegistrationInvitationData[]
> = async function (_, __, { signal }) {
    const { url, method } = ListRegistrationInvitations();

    const response = await fetch(url, { method, signal });
    const json = await response.json();

    return json;
};

export const fetchInvitation: ResourceFetcher<
    string,
    RegistrationInvitationData
> = async function (id, __, { signal }) {
    const { url, method } = GetRegistrationInvitation(id);

    const response = await fetch(url, { method, signal });

    if (response.status == 404) {
        throw new NotFoundResourceError();
    }

    const json = await response.json();

    return json;
};
