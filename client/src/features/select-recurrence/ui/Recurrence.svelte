<script lang="ts">
    import { HistoryView } from "$/shared/integrations/inertia";
    import { dispatchInput } from "$/shared/lib/dom/dispatch-input";
    import { DeferUntilNextFrame } from "$/shared/lib/svelte/defer-until-next-frame.svelte";
    import { tick } from "svelte";

    import BasicMenu from "./BasicMenu.svelte";
    import CustomConfigurator from "./CustomConfigurator.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        tooltip: string;
        day: ZonedDateTime;
        rrule?: string | null;
        name: string;
        deferHistoryViewFrames?: number;
    };

    let {
        tooltip,
        day,
        rrule = $bindable(),
        name,
        deferHistoryViewFrames = 0
    }: Props = $props();

    const customPickerView = new HistoryView<{
        __selectrecurrence: { isOpen: boolean };
    }>();
    const deferredView = new DeferUntilNextFrame(() => deferHistoryViewFrames);

    let rruleAnnouncer = $state<HTMLInputElement | null>(null);
    let isMenuOpen = $state(false);

    async function onSelect(r: string | null) {
        rrule = r;
        await tick();
        dispatchInput(rruleAnnouncer);
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
        () =>
            deferredView.ready &&
            (customPickerView.meta?.__selectrecurrence?.isOpen ?? false),
        (v) => {
            if (!v) {
                void customPickerView.back();
            }
        }
    }
/>

<input {name} bind:this={rruleAnnouncer} hidden value={rrule} />
