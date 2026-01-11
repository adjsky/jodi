import { getAll } from "$/generated/actions/App/Http/Controllers/FriendsController";

import type { ResourceFetcher } from "runed";

export const fetchFriends: ResourceFetcher<unknown, App.Data.FriendDto[]> =
    async function (_, __, { signal }) {
        const { url, method } = getAll();

        const response = await fetch(url, { method, signal });
        const json = await response.json();

        return json as App.Data.FriendDto[];
    };
