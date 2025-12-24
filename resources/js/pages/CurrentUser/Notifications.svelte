<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Bell } from "@lucide/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";

    const variants = ["push", "mail"] as const;

    const user = $derived($page.props.auth.user);
</script>

<SettingsLayout title={m["current-user.app-settings.notifications"]()}>
    <User.Info.Block class="py-5">
        {#each variants as variant (variant)}
            <User.Info.SelectRow
                href={update()}
                data={{ preferences: { notifications: variant } }}
                selected={variant == user.preferences.notifications}
            >
                {#snippet icon()}
                    <Bell />
                {/snippet}
                {m[`current-user.notifications.${variant}`]()}
            </User.Info.SelectRow>
        {/each}
    </User.Info.Block>
</SettingsLayout>
