<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell } from "@lucide/svelte";
    import AppLayout from "$/app/ui/layouts/AppLayout.svelte";
    import { User } from "$/entities/user";
    import {
        email,
        friends,
        invitations,
        language,
        name,
        weekStart
    } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { logout } from "$/generated/actions/App/Http/Controllers/LoginController";
    import { home } from "$/generated/routes";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { link } from "$/shared/inertia/link";
    import { LANGUAGES } from "$/shared/lib/language";
    import BackButton from "$/shared/ui/BackButton.svelte";

    const user = $derived($page.props.auth.user);
</script>

<AppLayout class="min-h-svh px-4 pt-3 pb-8">
    <header class="flex items-center justify-between">
        <BackButton {@attach link(() => ({ href: home() }))} />
        <div
            class="absolute top-4 left-1/2 flex -translate-x-1/2 flex-col items-center gap-1.5"
        >
            <User.Avatar name={user.name} class="size-10 text-xl" />
            <h1 class="text-xl font-bold">
                {user.name}
            </h1>
        </div>
        <button class="p-2.5 disabled:text-cream-400" disabled>
            <Bell class="text-3xl " />
        </button>
    </header>

    <User.Info.Block class="mt-13" title={m["current-user.account.title"]()}>
        <User.Info.SettingRow
            href={name()}
            title={m["current-user.account.name"]()}
        >
            {user.name}
        </User.Info.SettingRow>
        <User.Info.SettingRow
            href={email()}
            title={m["current-user.account.email"]()}
        >
            {user.email}
        </User.Info.SettingRow>
        <User.Info.SettingRow
            href={friends()}
            title={m["current-user.account.friends"]()}
        >
            0
        </User.Info.SettingRow>
        <User.Info.SettingRow
            href={invitations()}
            title={m["current-user.account.invitations"]()}
        >
            0
        </User.Info.SettingRow>
    </User.Info.Block>

    <User.Info.Block
        class="mt-10"
        title={m["current-user.app-settings.title"]()}
    >
        <User.Info.SettingRow
            href={language()}
            title={m["current-user.app-settings.language"]()}
        >
            {LANGUAGES[getLocale()]}
        </User.Info.SettingRow>
        <User.Info.SettingRow
            href={weekStart()}
            title={m["current-user.app-settings.week-start"]()}
        >
            {m[`current-user.days.${user.preferences.weekStartOn}`]()}
        </User.Info.SettingRow>
    </User.Info.Block>

    <User.Info.Block class="mt-10">
        <User.Info.ActionRow href={logout()}>
            {m["current-user.actions.log-out"]()}
        </User.Info.ActionRow>
    </User.Info.Block>

    <p class="mt-4 text-sm">&lt;{$page.version}&gt;</p>
</AppLayout>
