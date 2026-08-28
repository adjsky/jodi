<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell, BellRing, Mail } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import UpdateUser from "$/generated/actions/App/Domain/Identity/Actions/UpdateUser";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { Push } from "$/shared/services/push";
    import Button from "$/shared/ui/Button.svelte";

    import type { ViewProps } from "../model/view";

    let { open = $bindable() }: ViewProps = $props();

    const user = $derived(page.props.auth.user);
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.app-settings.notifications"]()} />
    <ScreenView.Content class="py-5">
        <User.Info.Block>
            <User.Info.SelectRow
                href={UpdateUser()}
                data={{ preferences: { notifications: "push" } }}
                selected={user.preferences.notifications == "push"}
            >
                {#snippet icon()}
                    <Bell />
                {/snippet}
                {m[`current-user.notifications.push`]()}
            </User.Info.SelectRow>
            <User.Info.SelectRow
                href={UpdateUser()}
                data={{ preferences: { notifications: "mail" } }}
                selected={user.preferences.notifications == "mail"}
            >
                {#snippet icon()}
                    <Mail />
                {/snippet}
                {m[`current-user.notifications.mail`]()}
            </User.Info.SelectRow>
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
