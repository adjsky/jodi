<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Calendar } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import UpdateUser from "$/generated/actions/App/Domain/Identity/Actions/UpdateUser";
    import { m } from "$/paraglide/messages";
    import FloatingView from "$/shared/ui/FloatingView.svelte";

    import { back } from "./Back.svelte";

    const days = ["monday", "sunday"] as const;

    const user = $derived($page.props.auth.user);
</script>

<FloatingView {back} title={m["current-user.app-settings.week-start"]()}>
    <User.Info.Block class="py-5">
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
</FloatingView>
