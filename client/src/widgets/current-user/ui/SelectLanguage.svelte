<script lang="ts">
    import { Languages } from "@lucide/svelte";
    import { createMutation } from "@tanstack/svelte-query";
    import { User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { LANGUAGES } from "#shared/cfg/constants.ts";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";

    import { updateUserPreferencesMutationOptions } from "../api/preferences.ts";

    import type { ViewProps } from "../model/types.ts";

    let { open = $bindable(), user }: ViewProps = $props();

    const updateUserPreferencesMutation = createMutation(
        () => updateUserPreferencesMutationOptions
    );
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.preferences.language"]()} />
    <ScreenView.Content class="py-5">
        <User.Info.Block>
            {#each Object.entries(LANGUAGES) as [locale, language] (locale)}
                <User.Info.SelectRow
                    selected={locale === user.preferences.locale}
                    onclick={() => {
                        updateUserPreferencesMutation.mutate({ locale });
                    }}
                >
                    {#snippet icon()}
                        <Languages />
                    {/snippet}
                    {language}
                </User.Info.SelectRow>
            {/each}
        </User.Info.Block>
    </ScreenView.Content>
</ScreenView.Overlay>
