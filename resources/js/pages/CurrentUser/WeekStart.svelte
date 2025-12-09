<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Calendar } from "@lucide/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";

    const days = ["monday", "sunday"] as const;

    const user = $derived($page.props.auth.user);
</script>

<SettingsLayout title={m["current-user.app-settings.week-start"]()}>
    <User.Info.Block class="py-5">
        {#each days as day (day)}
            <User.Info.SelectRow
                href={update()}
                data={{ preferences: { weekStartOn: day } }}
                selected={day == user.preferences.weekStartOn}
            >
                {#snippet icon()}
                    <Calendar />
                {/snippet}
                {m[`current-user.days.${day}`]()}
            </User.Info.SelectRow>
        {/each}
    </User.Info.Block>
</SettingsLayout>
