import {
    get,
    getAll
} from "$/generated/actions/App/Http/Controllers/RegistrationInvitationController";

import type { ResourceFetcher } from "runed";

export class NotFoundResourceError extends Error {}

export const fetchInvitations: ResourceFetcher<
    unknown,
    App.Data.RegistrationInvitationDto[]
> = async function (_, __, { signal }) {
    const { url, method } = getAll();

    const response = await fetch(url, { method, signal });
    const json = await response.json();

    return json as App.Data.RegistrationInvitationDto[];
};

export const fetchInvitation: ResourceFetcher<
    string,
    App.Data.RegistrationInvitationDto
> = async function (id, __, { signal }) {
    const { url, method } = get(id);

    const response = await fetch(url, { method, signal });

    if (response.status == 404) {
        throw new NotFoundResourceError();
    }

    const json = await response.json();

    return json as App.Data.RegistrationInvitationDto;
};
