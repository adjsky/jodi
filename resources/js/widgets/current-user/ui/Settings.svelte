<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { logout } from "$/generated/routes";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { LANGUAGES } from "$/shared/lib/language";
    import * as PushNotifications from "$/shared/lib/push-notifications";
    import FloatingView from "$/shared/ui/FloatingView.svelte";

    import { buildViewName, view } from "../model/view";
    import { back } from "./Back.svelte";
    import EditEmail from "./EditEmail.svelte";
    import EditName from "./EditName.svelte";
    import Friends from "./Friends.svelte";
    import Invitations from "./Invitations.svelte";
    import SelectLanguage from "./SelectLanguage.svelte";
    import SelectNotification from "./SelectNotification.svelte";
    import SelectWeekStart from "./SelectWeekStart.svelte";

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
            component: SelectLanguage
        },
        {
            name: "week-start",
            value: m[
                `current-user.week-start.${user.preferences.weekStartOn}`
            ](),
            component: SelectWeekStart
        },
        {
            name: "notifications",
            value: m[
                `current-user.notifications.${user.preferences.notifications}`
            ](),
            component: SelectNotification
        }
    ] as const);

    function onRowClick(name: string) {
        void view.push(buildViewName(name));
    }
</script>

{#if view.isOpen("me")}
    <FloatingView {back} class="pb-8">
        {#snippet title()}
            <div
                class="absolute top-1 left-1/2 flex -translate-x-1/2 flex-col items-center gap-1.5"
            >
                <User.Avatar
                    as="div"
                    name={user.name}
                    class="size-10 text-xl"
                />
                <h1 class="text-xl font-bold">
                    {user.name}
                </h1>
            </div>
        {/snippet}
        {#snippet action()}
            <button class="p-2.5 disabled:text-cream-400" disabled>
                <Bell class="text-3xl " />
            </button>
        {/snippet}

        <User.Info.Block
            title={m["current-user.account.title"]()}
            class="mt-13"
        >
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
            {#each appSettingsRows as { name, value } (name)}
                <User.Info.SettingRow
                    title={m[`current-user.app-settings.${name}`]()}
                    onclick={() => onRowClick(name)}
                >
                    {value}
                </User.Info.SettingRow>
            {/each}
        </User.Info.Block>

        <User.Info.Block class="mt-10">
            <User.Info.ActionRow
                href={logout()}
                onBefore={async () => {
                    await PushNotifications.destroySubscription();
                    PushNotifications.showConfigureBannerAgain();
                }}
                viewTransition
            >
                {m["current-user.log-out"]()}
            </User.Info.ActionRow>
        </User.Info.Block>

        <p class="mt-4 text-sm">v.{$page.props.version}</p>
    </FloatingView>
{/if}

{#each [...accountRows, ...appSettingsRows] as { name, component: Component } (name)}
    {#if view.name.startsWith(buildViewName(name))}
        <Component />
    {/if}
{/each}
