<script lang="ts">
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";
    import SadCat from "$/shared/assets/sad-cat.svg";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
    import { resource } from "runed";

    import { fetchFriends } from "../api/friends";
    import { back } from "./Back.svelte";

    const friendsResource = resource(() => [], fetchFriends);
</script>

<FloatingView {back} title={m["current-user.account.friends"]()}>
    <div class="flex grow flex-col justify-between py-5">
        <div
            class={[
                "flex grow flex-col pb-5",
                friendsResource.current?.length === 0 || friendsResource.error
                    ? "justify-center"
                    : "gap-2"
            ]}
        >
            {#if friendsResource.error}
                <img
                    src={SadCat}
                    width={82}
                    height={85}
                    alt=""
                    loading="lazy"
                    decoding="async"
                    class="mx-auto w-full max-w-28"
                />
                <p
                    class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium"
                >
                    {m["current-user.friends.error"]()}
                </p>
            {:else if friendsResource.loading}
                {#each Array.from({ length: 5 }) as _, idx (idx)}
                    {@render row()}
                {/each}
            {:else}
                {#each friendsResource.current as friend (friend.id)}
                    {@render row(friend)}
                {:else}
                    <img
                        src={Jelly}
                        width={82}
                        height={85}
                        alt=""
                        loading="lazy"
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
        </div>
        <Button disabled>
            {m["current-user.friends.add"]()}
        </Button>
    </div>
</FloatingView>

{#snippet row(friend?: App.Data.FriendDto)}
    <div
        class="border-gray-950 flex items-center gap-3 rounded-xl border bg-white px-4 py-3"
    >
        {#if !friend}
            <Skeleton class="size-9 rounded-full" />
        {:else}
            <User.Avatar as="div" name={friend.name} />
        {/if}
        <div>
            <p class="font-semibold">
                {#if !friend}
                    <Skeleton class="w-20" />
                {:else}
                    {friend.name}
                {/if}
            </p>
            <p class="text-sm text-cream-400">
                {#if !friend}
                    <Skeleton class="w-40" />
                {:else}
                    {friend.email}
                {/if}
            </p>
        </div>
    </div>
{/snippet}
