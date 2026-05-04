import ListFriends from "$/generated/actions/App/Domain/Identity/Actions/ListFriends";

import type { FriendData } from "$/entities/user/model/types";
import type { ResourceFetcher } from "runed";

export const fetchFriends: ResourceFetcher<unknown, FriendData[]> =
    async function (_, __, { signal }) {
        const { url, method } = ListFriends();

        const response = await fetch(url, { method, signal });
        const json = await response.json();

        return json;
    };
