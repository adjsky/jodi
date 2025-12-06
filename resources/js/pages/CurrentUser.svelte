<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell } from "@lucide/svelte";
    import AppLayout from "$/app/ui/layouts/AppLayout.svelte";
    import { User } from "$/entities/user";
    import { logout } from "$/generated/actions/App/Http/Controllers/LoginController";
    import { m } from "$/paraglide/messages";
    import { link } from "$/shared/inertia/link";
    import BackButton from "$/shared/ui/BackButton.svelte";
    import { UserConfiguration } from "$/widgets/current-user";

    const user = $derived($page.props.auth.user);
</script>

<AppLayout class="min-h-svh px-4 pt-3 pb-8">
    <header class="mb-13 flex items-center justify-between">
        <BackButton {@attach link(() => ({ href: "/" }))} />
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

    <UserConfiguration {user} />

    <User.Info.Block class="mt-10">
        <User.Info.ActionRow {@attach link(() => ({ href: logout() }))}>
            {m["current-user.actions.log-out"]()}
        </User.Info.ActionRow>
        <User.Info.ActionRow class="text-red disabled:opacity-50" disabled>
            {m["current-user.actions.delete-account"]()}
        </User.Info.ActionRow>
    </User.Info.Block>

    <p class="mt-4 text-sm">&lt;{$page.version}&gt;</p>
</AppLayout>
