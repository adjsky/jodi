<script lang="ts">
    import { normalizeIsoString } from "$/shared/lib/date";
    import { announce } from "$/shared/lib/form";
    import { tick } from "svelte";

    import { durationToZonedDT } from "../helpers/duration";
    import BasicMenu from "./BasicMenu.svelte";
    import CustomConfigurator from "./CustomConfigurator.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        tooltip: string;
        notifyAt: ZonedDateTime | null;
        startsAt: ZonedDateTime;
        name: string;
        beforeOpen?: () => void | boolean;
    };

    let {
        tooltip,
        notifyAt = $bindable(),
        startsAt,
        name,
        beforeOpen
    }: Props = $props();

    let announcerInput = $state<HTMLInputElement | null>(null);
    let isMenuOpen = $state(false);
    let isCustomPickerOpen = $state(false);

    async function onSelect(duration: string) {
        notifyAt = durationToZonedDT(startsAt, duration);
        await tick();
        announce(announcerInput);
    }
</script>

<BasicMenu
    {notifyAt}
    {startsAt}
    {tooltip}
    {onSelect}
    bind:open={
        () => isMenuOpen,
        (v) => {
            if (v && beforeOpen?.() === false) return;
            isMenuOpen = v;
        }
    }
    onCustomTrigger={() => {
        isMenuOpen = false;
        isCustomPickerOpen = true;
    }}
/>

<CustomConfigurator
    {notifyAt}
    {startsAt}
    {onSelect}
    bind:open={isCustomPickerOpen}
/>

<input
    bind:this={announcerInput}
    hidden
    value={notifyAt ? normalizeIsoString(notifyAt.toAbsoluteString()) : null}
    {name}
/>
