<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { User } from "$/entities/user";
    import LogoutUser from "$/generated/actions/App/Domain/Identity/Actions/LogoutUser";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { LANGUAGES } from "$/shared/cfg/constants";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { Push } from "$/shared/services/push";
    import { resource } from "runed";

    import { fetchFriends } from "../api/friends";
    import { fetchInvitations } from "../api/invitations";
    import { context } from "../model/context";
    import { buildViewName, view } from "../model/view";
    import { back } from "./Back.svelte";
    import EditEmail from "./EditEmail.svelte";
    import EditName from "./EditName.svelte";
    import Friends from "./Friends.svelte";
    import Invitations from "./Invitations.svelte";
    import SelectLanguage from "./SelectLanguage.svelte";
    import SelectNotification from "./SelectNotification.svelte";
    import SelectWeekStart from "./SelectWeekStart.svelte";
    import Warning from "./Warning.svelte";

    const friends = resource(() => [], fetchFriends);
    const invitations = resource(() => [], fetchInvitations);

    context.set({ friends, invitations });

    const user = $derived($page.props.auth.user);
    const { nInvitations, nFriends } = $derived($page.props.me);

    const accountRows = $derived([
        {
            name: "name",
            value: user.name,
            component: EditName
        },
        {
            name: "email",
            value: user.email,
            component: EditEmail
        },
        {
            name: "friends",
            value: nFriends,
            component: Friends
        },
        {
            name: "invitations",
            value: nInvitations,
            component: Invitations
        }
    ] as const);

    const appSettingsRows = $derived([
        {
            name: "language",
            value: LANGUAGES[getLocale()],
            component: SelectLanguage,
            warning: false
        },
        {
            name: "week-start",
            value: m[
                `current-user.week-start.${user.preferences.weekStartOn}`
            ](),
            component: SelectWeekStart,
            warning: false
        },
        {
            name: "notifications",
            value: m[
                `current-user.notifications.${user.preferences.notifications}`
            ](),
            component: SelectNotification,
            warning: Push.subscription.needsConfiguration
        }
    ] as const);

    function onRowClick(name: string) {
        void view.push(buildViewName(name));
    }
</script>

{#if view.name.startsWith("me")}
    <ScreenView.Overlay class="pb-0">
        <ScreenView.Header {back} title={m["current-user.settings"]()} />
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

            <p class="mt-4 text-sm">v.{$page.props.version}</p>
        </ScreenView.Content>
    </ScreenView.Overlay>
{/if}

{#each [...accountRows, ...appSettingsRows] as { name, component: Component } (name)}
    {#if view.name.startsWith(buildViewName(name))}
        <Component />
    {/if}
{/each}
