<script lang="ts">
    import { Calendar } from "@lucide/svelte";
    import { createMutation } from "@tanstack/svelte-query";
    import { User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";

    import { updateUserPreferencesMutationOptions } from "../api/preferences.ts";

    import type { ViewProps } from "../model/types.ts";

    let { open = $bindable(), user }: ViewProps = $props();

    const days = ["monday", "sunday"] as const;

    const updateUserPreferencesMutation = createMutation(
        () => updateUserPreferencesMutationOptions
    );
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.preferences.week-start"]()} />
    <ScreenView.Content class="py-5">
        <User.Info.Block>
            {#each days as day (day)}
                <User.Info.SelectRow
                    selected={day === user.preferences.weekStartOn}
                    onclick={() => {
                        updateUserPreferencesMutation.mutate({
                            weekStartOn: day
                        });
                    }}
                >
                    {#snippet icon()}
                        <Calendar />
                    {/snippet}
                    {m[`current-user.week-start.${day}`]()}
                </User.Info.SelectRow>
            {/each}
        </User.Info.Block>
    </ScreenView.Content>
</ScreenView.Overlay>
