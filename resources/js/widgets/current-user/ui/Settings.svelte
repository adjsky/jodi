<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { useQueryClient } from "@tanstack/svelte-query";
    import { User } from "$/entities/user";
    import LogoutUser from "$/generated/actions/App/Domain/Identity/Actions/LogoutUser";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { LANGUAGES } from "$/shared/cfg/constants";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { noop } from "$/shared/lib/function/noop";
    import { Push } from "$/shared/services/push";
    import { onMount } from "svelte";

    import { friendsQueryOptions } from "../api/friends";
    import { invitationsQueryOptions } from "../api/invitations";
    import { buildViewName, view } from "../model/view";
    import EditEmail from "./EditEmail.svelte";
    import EditName from "./EditName.svelte";
    import Friends from "./Friends.svelte";
    import Invitations from "./Invitations.svelte";
    import SelectLanguage from "./SelectLanguage.svelte";
    import SelectNotification from "./SelectNotification.svelte";
    import SelectWeekStart from "./SelectWeekStart.svelte";
    import Warning from "./Warning.svelte";

    const queryClient = useQueryClient();

    onMount(() => {
        void queryClient.query(friendsQueryOptions).catch(noop);
        void queryClient.query(invitationsQueryOptions).catch(noop);
    });

    const user = $derived(page.props.auth.user);
    const { nInvitations, nFriends } = $derived(page.props.me);

    const accountRows = $derived([
        {
            name: "name",
            value: user.name,
            view: EditName
        },
        {
            name: "email",
            value: user.email,
            view: EditEmail
        },
        {
            name: "friends",
            value: nFriends,
            view: Friends
        },
        {
            name: "invitations",
            value: nInvitations,
            view: Invitations
        }
    ] as const);

    const appSettingsRows = $derived([
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
            warning: Push.subscription.needsConfiguration
        }
    ] as const);

    function onRowClick(name: string) {
        void view.push(buildViewName(name));
    }
</script>

<ScreenView.Overlay
    bind:open={() => view.name.startsWith("me"), () => view.back()}
    class="pb-0"
>
    <ScreenView.Header title={m["current-user.settings"]()} />
    <ScreenView.Content
        class="overflow-y-auto overscroll-contain pt-5 pb-safe-offset-8"
    >
        <User.Info.Block title={m["current-user.account.title"]()}>
            {#each accountRows as { name, value } (name)}
                <User.Info.SettingRow
                    title={m[`current-user.account.${name}`]()}
                    onclick={() => onRowClick(name)}
                >
                    {value}
                </User.Info.SettingRow>
            {/each}
        </User.Info.Block>
        <User.Info.Block
            title={m["current-user.app-settings.title"]()}
            class="mt-10"
        >
            {#each appSettingsRows as { name, value, warning } (name)}
                <User.Info.SettingRow
                    title={m[`current-user.app-settings.${name}`]()}
                    onclick={() => onRowClick(name)}
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
            <User.Info.ActionRow href={LogoutUser()} viewTransition>
                {m["current-user.log-out"]()}
            </User.Info.ActionRow>
        </User.Info.Block>

        <p class="mt-4 text-sm">v.{page.props.version}</p>
    </ScreenView.Content>
</ScreenView.Overlay>

{#each [...accountRows, ...appSettingsRows] as { name, view: View } (name)}
    <View
        bind:open={
            () => view.name.startsWith(buildViewName(name)), () => view.back()
        }
    />
{/each}
