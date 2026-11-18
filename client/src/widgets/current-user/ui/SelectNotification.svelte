<script lang="ts">
    import { Bell, BellRing, Mail } from "@lucide/svelte";
    import { createMutation } from "@tanstack/svelte-query";
    import { User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { NotificationCoordinator } from "#shared/capabilities/notification-coordinator/mod.svelte.ts";
    import { PLATFORM } from "#shared/cfg/capacitor.ts";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import { toaster } from "#shared/ui/toaster.ts";

    import { update_user_preferences_mutation_options } from "../api/preferences.ts";

    import type { ViewProps } from "../model/types.ts";

    let { open = $bindable(), user }: ViewProps = $props();

    const rows = [
        { channel: "push", icon: Bell },
        { channel: "mail", icon: Mail }
    ] as const;

    const update_user_preferences_mutation = createMutation(
        () => update_user_preferences_mutation_options
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
                        update_user_preferences_mutation.mutate({
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

        {#if user.preferences.notifications === "push" && NotificationCoordinator.permission !== "granted"}
            <Button
                type="button"
                class="mt-5 gap-2"
                onclick={() => {
                    if (NotificationCoordinator.permission === "denied") {
                        toaster.info(
                            PLATFORM === "web"
                                ? m["notification.permission-denied.web"]()
                                : m["notification.permission-denied.native"]()
                        );
                    }

                    void NotificationCoordinator.enable();
                }}
            >
                <BellRing class="text-xl" />
                {m["current-user.notifications.allow"]()}
            </Button>
        {/if}
    </ScreenView.Content>
</ScreenView.Overlay>
