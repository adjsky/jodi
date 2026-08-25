import { queryOptions } from "@tanstack/svelte-query";
import ListFriends from "$/generated/actions/App/Domain/Identity/Actions/ListFriends";
import ky from "ky";

import type { FriendData } from "$/entities/user";

export const friendsQueryOptions = queryOptions<FriendData[]>({
    queryKey: ["friends"],
    queryFn({ signal }) {
        const { url, method } = ListFriends();

        return ky(url, { method, signal }).json();
    }
});
