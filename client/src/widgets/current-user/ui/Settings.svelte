<script lang="ts">
    import { createQuery } from "@tanstack/svelte-query";
    import { SessionCache, User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { getLocale } from "#paraglide/runtime.js";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { LANGUAGES, VERSION } from "#shared/cfg/constants.ts";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import Loader from "#shared/ui/Loader.svelte";

    import { friendsQueryOptions } from "../api/friends";
    import { invitationsQueryOptions } from "../api/invitations";
    import EditEmail from "./EditEmail.svelte";
    import EditName from "./EditName.svelte";
    import Friends from "./Friends.svelte";
    import Invitations from "./Invitations.svelte";
    import Logout from "./Logout.svelte";
    import SelectLanguage from "./SelectLanguage.svelte";
    import SelectNotification from "./SelectNotification.svelte";
    import SelectWeekStart from "./SelectWeekStart.svelte";
    import Warning from "./Warning.svelte";

    const invitations = createQuery(() => invitationsQueryOptions);
    const friends = createQuery(() => friendsQueryOptions);

    const session = SessionCache.retain();
    const user = $derived(session.current.user);

    const accountRows = $derived([
        {
            name: "name",
            value: user.name,
            isLoading: false,
            view: EditName
        },
        {
            name: "email",
            value: user.email,
            isLoading: false,
            view: EditEmail
        },
        {
            name: "friends",
            value: friends.data?.length ?? 0,
            isLoading: friends.isLoading,
            view: Friends
        },
        {
            name: "invitations",
            value: invitations.data?.length ?? 0,
            isLoading: invitations.isLoading,
            view: Invitations
        }
    ] as const);

    const preferencesRows = $derived([
        {
            name: "language",
            value: LANGUAGES[getLocale()],
            view: SelectLanguage,
            warning: false
        },
        {
            name: "week-start",
            value: m[
                `current-user.week-start.${user.preferences.weekStartOn}`
            ](),
            view: SelectWeekStart,
            warning: false
        },
        {
            name: "notifications",
            value: m[
                `current-user.notifications.${user.preferences.notifications}`
            ](),
            view: SelectNotification,
            warning: false
            // warning: Push.subscription.needsConfiguration
        }
    ] as const);
</script>

<ScreenView.Overlay
    bind:open={
        () => Overlay.name?.startsWith("me") ?? false, () => Overlay.close()
    }
    class="pb-0"
>
    <ScreenView.Header title={m["current-user.settings"]()} />
    <ScreenView.Content
        class="overflow-y-auto overscroll-y-contain pt-5 pb-safe-offset-8"
    >
        <User.Info.Block title={m["current-user.account.title"]()}>
            {#each accountRows as { name, value, isLoading } (name)}
                <User.Info.SettingRow
                    title={m[`current-user.account.${name}`]()}
                    onclick={() => Overlay.open(`me/${name}`)}
                >
                    {#if isLoading}
                        <Loader />
                    {:else}
                        {value}
                    {/if}
                </User.Info.SettingRow>
            {/each}
        </User.Info.Block>
        <User.Info.Block
            title={m["current-user.preferences.title"]()}
            class="mt-10"
        >
            {#each preferencesRows as { name, value, warning } (name)}
                <User.Info.SettingRow
                    title={m[`current-user.preferences.${name}`]()}
                    onclick={() => Overlay.open(`me/${name}`)}
                >
                    {#snippet indicator()}
                        {#if warning}
                            <Warning />
                        {/if}
                    {/snippet}
                    {value}
                </User.Info.SettingRow>
            {/each}
        </User.Info.Block>

        <User.Info.Block class="mt-10">
            <Logout />
        </User.Info.Block>

        <p class="mt-4 text-sm">v.{VERSION}</p>
    </ScreenView.Content>
</ScreenView.Overlay>

{#each [...accountRows, ...preferencesRows] as { name, view: View } (name)}
    <View
        bind:open={() => Overlay.isOpen(`me/${name}`), () => Overlay.close()}
        {user}
    />
{/each}
