import { queryOptions } from "@tanstack/svelte-query";
import { api } from "#shared/api/client.ts";
import ListFriends from "#wayfinder/actions/App/Domain/Identity/Actions/ListFriends.ts";

import type { FriendData } from "#entities/user/mod.ts";

export const friendsQueryOptions = queryOptions<FriendData[]>({
    queryKey: ["friends"],
    queryFn({ signal }) {
        const { url, method } = ListFriends();
        return api(url, { method, signal }).json();
    }
});
