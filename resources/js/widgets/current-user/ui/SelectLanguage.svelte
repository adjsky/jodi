<script lang="ts">
    import { Languages } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { LANGUAGES } from "$/shared/lib/language";
    import FloatingView from "$/shared/ui/FloatingView.svelte";

    import { back } from "./Back.svelte";
</script>

<FloatingView {back} title={m["current-user.app-settings.language"]()}>
    <User.Info.Block class="py-5">
        {#each Object.entries(LANGUAGES) as [locale, language] (locale)}
            <User.Info.SelectRow
                href={update()}
                data={{ preferences: { locale } }}
                selected={locale == getLocale()}
                onSuccess={() => location.reload()}
            >
                {#snippet icon()}
                    <Languages />
                {/snippet}
                {language}
            </User.Info.SelectRow>
        {/each}
    </User.Info.Block>
</FloatingView>
