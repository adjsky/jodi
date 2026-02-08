<script lang="ts">
    import { page, progress } from "@inertiajs/svelte";
    import { Bell, BellRing, Mail } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import * as PushNotifications from "$/shared/lib/push-notifications";
    import { toaster } from "$/shared/lib/toaster";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";

    import { back } from "./Back.svelte";

    const user = $derived($page.props.auth.user);

    let showAllowPushButton = $state(false);

    $effect(() => {
        if (user.preferences.notifications != "push") {
            showAllowPushButton = false;
            return;
        }

        const hasSupport = PushNotifications.checkSupport();
        if (!hasSupport) return;

        void PushNotifications.checkSubscription().then((hasSubscription) => {
            showAllowPushButton = !hasSubscription;
        });
    });
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

    {#if showAllowPushButton}
        <Button
            class="gap-2"
            onclick={async () => {
                try {
                    progress.reveal(true);
                    progress.start();
                    await PushNotifications.subscribe();
                    showAllowPushButton = false;
                    progress.finish();
                    toaster.success(
                        m["push-notifications.success-subscribe"]()
                    );
                } catch (e) {
                    console.error(e);
                    progress.remove();
                    if (e instanceof Error && e.name == "NotAllowedError") {
                        toaster.info(m["push-notifications.no-permission"]());
                    } else {
                        toaster.error(
                            m["push-notifications.failed-to-subscribe"]()
                        );
                    }
                }
            }}
        >
            <BellRing class="text-xl" />
            {m["current-user.notifications.allow"]()}
        </Button>
    {/if}
</FloatingView>
