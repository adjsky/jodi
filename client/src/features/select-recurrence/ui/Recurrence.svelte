<script lang="ts">
    import BasicMenu from "./BasicMenu.svelte";
    import CustomConfigurator from "./CustomConfigurator.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        tooltip: string;
        day: ZonedDateTime;
        rrule?: string | null;
    };

    let { tooltip, day, rrule = $bindable() }: Props = $props();

    let isBasicOpen = $state(false);
    let isCustomOpen = $state(false);
</script>

<BasicMenu
    bind:open={isBasicOpen}
    {tooltip}
    {rrule}
    onSelect={(r) => (rrule = r)}
    onCustomTrigger={() => {
        isBasicOpen = false;
        isCustomOpen = true;
    }}
/>

<CustomConfigurator
    bind:open={isCustomOpen}
    {day}
    {rrule}
    onSelect={(r) => (rrule = r)}
/>
