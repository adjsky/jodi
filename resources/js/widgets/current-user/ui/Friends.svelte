<script lang="ts">
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import { resource } from "runed";

    import { fetchFriends } from "../api/friends";
    import { back } from "./Back.svelte";

    const friends = resource(() => [], fetchFriends);
</script>

<FloatingView {back} title={m["current-user.account.friends"]()}>
    <div class="flex grow flex-col justify-between py-5">
        <div
            class={[
                "flex grow flex-col pb-5",
                friends.current?.length == 0 ? "justify-center" : "gap-2"
            ]}
        >
            {#if friends.current?.length === 0}
                <img
                    src={Jelly}
                    width={82}
                    height={85}
                    alt=""
                    loading="lazy"
                    decoding="async"
                    class="mx-auto max-w-32"
                />
                <p
                    class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium"
                >
                    {m["current-user.friends.no-friends"]()}
                </p>
            {:else}
                {#each friends.current as friend (friend.id)}
                    <div
                        class="border-gray-950 flex items-center gap-3 rounded-xl border bg-white px-4 py-3"
                    >
                        <User.Avatar name={friend.name} />
                        <div>
                            <p class="font-semibold">
                                {friend.name}
                            </p>
                            <p class="text-sm text-cream-400">
                                {friend.email}
                            </p>
                        </div>
                    </div>
                {/each}
            {/if}
        </div>
        <Button disabled>
            {m["current-user.friends.add"]()}
        </Button>
    </div>
</FloatingView>
