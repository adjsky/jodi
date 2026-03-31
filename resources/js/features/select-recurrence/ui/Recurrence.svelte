<script lang="ts">
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { announce } from "$/shared/lib/form";
    import { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";
    import { tick } from "svelte";

    import BasicMenu from "./BasicMenu.svelte";
    import CustomConfigurator from "./CustomConfigurator.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        tooltip: string;
        day: ZonedDateTime;
        rrule?: string | null;
        name: string;
    };

    let { tooltip, day, rrule = $bindable(), name }: Props = $props();

    const customPickerView = new HistoryView<{
        [DISABLE_SHEET_DRAGGING]: boolean;
        __selectrecurrence: { isOpen: boolean };
    }>();

    let rruleAnnouncer = $state<HTMLInputElement | null>(null);
    let isMenuOpen = $state(false);

    async function onSelect(r: string | null) {
        rrule = r;
        await tick();
        announce(rruleAnnouncer);
    }
</script>

<BasicMenu
    {tooltip}
    {rrule}
    {onSelect}
    bind:open={isMenuOpen}
    onCustomTrigger={() => {
        isMenuOpen = false;
        void customPickerView.push(customPickerView.name, {
            meta: {
                ...customPickerView.meta,
                [DISABLE_SHEET_DRAGGING]: true,
                __selectrecurrence: { isOpen: true }
            }
        });
    }}
/>

<CustomConfigurator
    {day}
    {rrule}
    {onSelect}
    bind:open={
        () => customPickerView.meta?.__selectrecurrence?.isOpen ?? false,
        (v) => {
            if (!v) {
                void customPickerView.back();
            }
        }
    }
/>

<input {name} bind:this={rruleAnnouncer} hidden value={rrule} />
