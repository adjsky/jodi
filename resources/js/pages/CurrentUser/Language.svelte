<script lang="ts">
    import { Languages } from "@lucide/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { me } from "$/generated/routes";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { LANGUAGES } from "$/shared/lib/language";
</script>

<SettingsLayout title={m["current-user.app-settings.language"]()}>
    <User.Info.Block class="py-5">
        {#each Object.entries(LANGUAGES) as [locale, language] (locale)}
            <User.Info.SelectRow
                href={update()}
                data={{ preferences: { locale } }}
                selected={locale == getLocale()}
                onSuccess={() => (location.href = me().url)}
            >
                {#snippet icon()}
                    <Languages />
                {/snippet}
                {language}
            </User.Info.SelectRow>
        {/each}
    </User.Info.Block>
</SettingsLayout>
