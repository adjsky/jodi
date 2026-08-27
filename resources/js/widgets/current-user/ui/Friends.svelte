<script lang="ts">
    import { createQuery } from "@tanstack/svelte-query";
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";
    import { ScreenView } from "$/shared/composites/screen-view";
    import Button from "$/shared/ui/Button.svelte";
    import ResourceError from "$/shared/ui/ResourceError.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";

    import { friendsQueryOptions } from "../api/friends";

    import type { ViewProps } from "../model/view";
    import type { FriendData } from "$/entities/user";

    let { open = $bindable() }: ViewProps = $props();

    const friends = createQuery(() => ({
        ...friendsQueryOptions,
        enabled: open
    }));

    const isError = $derived(!friends.data && friends.error);
    const isLoading = $derived(friends.isLoading);
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.account.friends"]()} />
    <ScreenView.Content
        class={[
            "overflow-y-auto overscroll-contain",
            !isError && "pt-5",
            friends.data?.length === 0 || isError ? "justify-center" : "gap-2"
        ]}
        aria-live="polite"
        aria-busy={friends.isFetching}
    >
        {#if isError}
            <ResourceError
                message={m["current-user.friends.error"]()}
                onRetry={() => friends.refetch()}
            />
        {:else if isLoading}
            {#each Array.from({ length: 5 }) as _, idx (idx)}
                {@render row()}
            {/each}
        {:else}
            {#each friends.data as friend (friend.id)}
                {@render row(friend)}
            {:else}
                <img
                    src={Jelly}
                    width={82}
                    height={85}
                    alt=""
                    decoding="async"
                    class="mx-auto w-full max-w-28"
                />
                <p
                    class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium"
                >
                    {m["current-user.friends.no-friends"]()}
                </p>
            {/each}
        {/if}
    </ScreenView.Content>
    <ScreenView.Footer class="mt-5">
        <Button type="button" disabled>
            {m["current-user.friends.add"]()}
        </Button>
    </ScreenView.Footer>
</ScreenView.Overlay>

{#snippet row(friend?: FriendData)}
    <div
        class="border-gray-950 flex items-center gap-3 rounded-xl border bg-white px-4 py-3"
    >
        {#if !friend}
            <Skeleton class="size-9 rounded-full" />
        {:else}
            <User.Avatar as="div" class="shrink-0" name={friend.name} />
        {/if}
        <div class="min-w-0 grow">
            <p class="truncate font-semibold">
                {#if !friend}
                    <Skeleton class="w-20" />
                {:else}
                    {friend.name}
                {/if}
            </p>
            <p class="truncate text-sm text-cream-400">
                {#if !friend}
                    <Skeleton class="w-40" />
                {:else}
                    {friend.email}
                {/if}
            </p>
        </div>
    </div>
{/snippet}
