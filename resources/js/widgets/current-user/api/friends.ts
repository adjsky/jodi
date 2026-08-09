import ListFriends from "$/generated/actions/App/Domain/Identity/Actions/ListFriends";
import ky from "ky";

import type { FriendData } from "$/entities/user";
import type { ResourceFetcher } from "runed";

export const fetchFriends: ResourceFetcher<unknown, FriendData[]> = function (
    _,
    __,
    { signal }
) {
    const { url, method } = ListFriends();

    return ky(url, { method, signal }).json();
};
