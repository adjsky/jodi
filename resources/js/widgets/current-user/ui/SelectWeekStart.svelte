<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Calendar } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import UpdateUser from "$/generated/actions/App/Domain/Identity/Actions/UpdateUser";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";

    import type { ViewProps } from "../model/view";

    let { open = $bindable() }: ViewProps = $props();

    const days = ["monday", "sunday"] as const;

    const user = $derived(page.props.auth.user);
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.app-settings.week-start"]()} />
    <ScreenView.Content class="py-5">
        <User.Info.Block>
            {#each days as day (day)}
                <User.Info.SelectRow
                    href={UpdateUser()}
                    data={{ preferences: { weekStartOn: day } }}
                    selected={day == user.preferences.weekStartOn}
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
