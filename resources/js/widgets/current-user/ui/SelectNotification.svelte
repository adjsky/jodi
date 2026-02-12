<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell, BellRing, Mail } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";

    import { back } from "./Back.svelte";

    const user = $derived($page.props.auth.user);
</script>

<FloatingView {back} title={m["current-user.app-settings.notifications"]()}>
    <User.Info.Block class="py-5">
        <User.Info.SelectRow
            href={update()}
            data={{ preferences: { notifications: "push" } }}
            selected={user.preferences.notifications == "push"}
        >
            {#snippet icon()}
                <Bell />
            {/snippet}
            {m[`current-user.notifications.push`]()}
        </User.Info.SelectRow>
        <User.Info.SelectRow
            href={update()}
            data={{ preferences: { notifications: "mail" } }}
            selected={user.preferences.notifications == "mail"}
        >
            {#snippet icon()}
                <Mail />
            {/snippet}
            {m[`current-user.notifications.mail`]()}
        </User.Info.SelectRow>
    </User.Info.Block>

    {#if PushSubscription.synchronization.needsConfiguration}
        <Button
            class="gap-2"
            onclick={() => {
                void PushSubscription.subscribe();
            }}
        >
            <BellRing class="text-xl" />
            {m["current-user.notifications.allow"]()}
        </Button>
    {/if}
</FloatingView>
