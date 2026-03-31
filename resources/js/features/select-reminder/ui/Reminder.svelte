<script lang="ts">
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { normalizeIsoString } from "$/shared/lib/date";
    import { announce } from "$/shared/lib/form";
    import { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";
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

    const customPickerView = new HistoryView<{
        [DISABLE_SHEET_DRAGGING]: boolean;
        __selectreminder: { isOpen: boolean };
    }>();

    let announcerInput = $state<HTMLInputElement | null>(null);
    let isMenuOpen = $state(false);

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
        void customPickerView.push(customPickerView.name, {
            meta: {
                ...customPickerView.meta,
                [DISABLE_SHEET_DRAGGING]: true,
                __selectreminder: { isOpen: true }
            }
        });
    }}
/>

<CustomConfigurator
    {notifyAt}
    {startsAt}
    {onSelect}
    bind:open={
        () => customPickerView.meta?.__selectreminder?.isOpen ?? false,
        (v) => {
            if (!v) {
                void customPickerView.back();
            }
        }
    }
/>

<input
    bind:this={announcerInput}
    hidden
    value={notifyAt ? normalizeIsoString(notifyAt.toAbsoluteString()) : null}
    {name}
/>
