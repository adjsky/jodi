<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell, BellRing, Mail } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import UpdateUser from "$/generated/actions/App/Domain/Identity/Actions/UpdateUser";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { Push } from "$/shared/services/push";
    import Button from "$/shared/ui/Button.svelte";

    import { preferences } from "../api/preferences";

    import type { ViewProps } from "../model/view";

    let { open = $bindable() }: ViewProps = $props();

    const user = $derived(page.props.auth.user);

    const rows = [
        { channel: "push", icon: Bell },
        { channel: "mail", icon: Mail }
    ] as const;
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.app-settings.notifications"]()} />
    <ScreenView.Content class="py-5">
        <User.Info.Block>
            {#each rows as { channel, icon: Icon } (channel)}
                <User.Info.SelectRow
                    {...preferences(
                        { notifications: channel },
                        m["current-user.notifications.error"]()
                    )}
                    href={UpdateUser()}
                    data={{ preferences: { notifications: channel } }}
                    selected={user.preferences.notifications == channel}
                >
                    {#snippet icon()}
                        <Icon />
                    {/snippet}
                    {m[`current-user.notifications.${channel}`]()}
                </User.Info.SelectRow>
            {/each}
        </User.Info.Block>

        {#if Push.subscription.needsConfiguration}
            <Button
                type="button"
                class="mt-5 gap-2"
                onclick={() => {
                    void Push.subscription.subscribe();
                }}
            >
                <BellRing class="text-xl" />
                {m["current-user.notifications.allow"]()}
            </Button>
        {/if}
    </ScreenView.Content>
</ScreenView.Overlay>
