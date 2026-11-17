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

    let is_basic_open = $state(false);
    let is_custom_open = $state(false);
</script>

<BasicMenu
    bind:open={is_basic_open}
    {tooltip}
    {rrule}
    on_select={(r) => (rrule = r)}
    on_custom_trigger={() => {
        is_basic_open = false;
        is_custom_open = true;
    }}
/>

<CustomConfigurator bind:open={is_custom_open} {day} {rrule} on_select={(r) => (rrule = r)} />
