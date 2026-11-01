<script lang="ts">
    import { Bell, Mail } from "@lucide/svelte";
    import { createMutation } from "@tanstack/svelte-query";
    import { User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";

    // import Button from "#shared/ui/Button.svelte";

    import { updateUserPreferencesMutationOptions } from "../api/preferences.ts";

    import type { ViewProps } from "../model/types.ts";

    let { open = $bindable(), user }: ViewProps = $props();

    const rows = [
        { channel: "push", icon: Bell },
        { channel: "mail", icon: Mail }
    ] as const;

    const updateUserPreferencesMutation = createMutation(
        () => updateUserPreferencesMutationOptions
    );
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.preferences.notifications"]()} />
    <ScreenView.Content class="py-5">
        <User.Info.Block>
            {#each rows as { channel, icon: Icon } (channel)}
                <User.Info.SelectRow
                    selected={user.preferences.notifications === channel}
                    onclick={() => {
                        updateUserPreferencesMutation.mutate({
                            notifications: channel
                        });
                    }}
                >
                    {#snippet icon()}
                        <Icon />
                    {/snippet}
                    {m[`current-user.notifications.${channel}`]()}
                </User.Info.SelectRow>
            {/each}
        </User.Info.Block>

        <!-- {#if Push.subscription.needsConfiguration}
            <Button
                type="button"
                class="mt-5 gap-2"
                onclick={() => {
                    void Push.subscription.subscribe();
                }}
                loading={Push.subscription.isSubscribing}
            >
                <BellRing class="text-xl" />
                {m["current-user.notifications.allow"]()}
            </Button>
        {/if} -->
    </ScreenView.Content>
</ScreenView.Overlay>
