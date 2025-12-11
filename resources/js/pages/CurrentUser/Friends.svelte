<script lang="ts">
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";

    type Props = {
        friends: App.Data.FriendDto[];
    };

    const { friends }: Props = $props();
</script>

<SettingsLayout title={m["current-user.account.friends"]()}>
    <div
        class={[
            "flex flex-col py-5",
            friends.length == 0 && "justify-center",
            friends.length > 0 && "gap-2"
        ]}
    >
        {#if friends.length == 0}
            <img
                src={Jelly}
                width={82}
                height={85}
                alt=""
                loading="lazy"
                decoding="async"
                class="mx-auto max-w-32"
            />
            <p class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium">
                {m["current-user.friends.no-friends"]()}
            </p>
        {:else}
            {#each friends as friend (friend.id)}
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
</SettingsLayout>
