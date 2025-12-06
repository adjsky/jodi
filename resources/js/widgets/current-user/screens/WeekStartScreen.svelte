<script lang="ts">
    import { Calendar } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import { link } from "$/shared/inertia/link";

    import Layout from "./Layout.svelte";

    import type { LayoutProps } from "../model/types";

    type Props = LayoutProps & { weekStart: string };

    const { weekStart, ...props }: Props = $props();

    const days = ["monday", "sunday"] as const;
</script>

<Layout {...props}>
    <User.Info.Block>
        {#each days as day (day)}
            <User.Info.SelectRow
                {@attach link(() => ({
                    href: update(),
                    data: { preferences: { weekStartOn: day } }
                }))}
                selected={day == weekStart}
            >
                {#snippet icon()}
                    <Calendar />
                {/snippet}
                {m[`current-user.days.${day}`]()}
            </User.Info.SelectRow>
        {/each}
    </User.Info.Block>
</Layout>
