<script lang="ts">
    import { inertia, page } from "@inertiajs/svelte";
    import { Bell, ChevronLeft } from "@lucide/svelte";
    import AppLayout from "$/app/ui/layouts/AppLayout.svelte";
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";

    const user = $derived($page.props.auth.user);
</script>

<AppLayout class="min-h-svh px-4 pt-3 pb-8">
    <header class="flex items-center justify-between">
        <button
            use:inertia={{
                href: "/"
            }}
            class="p-2"
        >
            <ChevronLeft class="text-4xl" />
        </button>
        <div
            class="absolute top-4 left-1/2 flex -translate-x-1/2 flex-col items-center gap-1.5"
        >
            <User.Avatar name={user.name} class="size-10 text-xl" />
            <h1 class="text-xl font-bold">
                {user.name}
            </h1>
        </div>
        <button class="p-2.5"><Bell class="text-3xl" /></button>
    </header>

    <User.Info.Block title={m["current-user.account.title"]()} class="mt-13">
        <User.Info.ModalRow title={m["current-user.account.name"]()}>
            {user.name}
        </User.Info.ModalRow>
        <User.Info.ModalRow title={m["current-user.account.email"]()}>
            {user.email}
        </User.Info.ModalRow>
        <User.Info.ModalRow title={m["current-user.account.friends"]()}>
            0
        </User.Info.ModalRow>
        <User.Info.ModalRow title={m["current-user.account.invitations"]()}>
            0
        </User.Info.ModalRow>
    </User.Info.Block>

    <User.Info.Block
        title={m["current-user.app-settings.title"]()}
        class="mt-10"
    >
        <User.Info.ModalRow title={m["current-user.app-settings.language"]()}>
            English
        </User.Info.ModalRow>
        <User.Info.ModalRow
            title={m["current-user.app-settings.week-start-on"]()}
        >
            Monday
        </User.Info.ModalRow>
        <User.Info.ModalRow
            title={m["current-user.app-settings.notifications"]()}
        >
            All
        </User.Info.ModalRow>
    </User.Info.Block>

    <User.Info.Block class="mt-10">
        <User.Info.ActionRow>
            {m["current-user.actions.log-out"]()}
        </User.Info.ActionRow>
        <User.Info.ActionRow class="text-red">
            {m["current-user.actions.delete-account"]()}
        </User.Info.ActionRow>
    </User.Info.Block>

    <p class="mt-4 text-sm">&lt;{$page.version}&gt;</p>
</AppLayout>
