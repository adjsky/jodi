<script lang="ts">
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";
    import SadCat from "$/shared/assets/sad-cat.svg";
    import { ScreenView } from "$/shared/composites/screen-view";
    import Button from "$/shared/ui/Button.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
    import { onMount } from "svelte";

    import { context } from "../model/context";
    import { back } from "./Back.svelte";

    import type { FriendData } from "$/entities/user";

    const { friends } = context.get();

    onMount(() => {
        if (friends.current && !friends.loading && !friends.error) {
            void friends.refetch();
        }
    });

    const isError = $derived(!friends.current && friends.error);
    const isLoading = $derived(!friends.current && friends.loading);
</script>

<ScreenView.Overlay>
    <ScreenView.Header {back} title={m["current-user.account.friends"]()} />
    <ScreenView.Content
        class={[
            "overflow-y-auto overscroll-contain pt-5",
            friends.current?.length === 0 || isError
                ? "justify-center"
                : "gap-2"
        ]}
        aria-live="polite"
        aria-busy={friends.loading}
    >
        {#if isError}
            <img
                src={SadCat}
                width={82}
                height={85}
                alt=""
                decoding="async"
                class="mx-auto w-full max-w-28"
            />
            <p class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium">
                {m["current-user.friends.error"]()}
            </p>
        {:else if isLoading}
            {#each Array.from({ length: 5 }) as _, idx (idx)}
                {@render row()}
            {/each}
        {:else}
            {#each friends.current as friend (friend.id)}
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
