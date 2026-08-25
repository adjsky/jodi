<script lang="ts">
    import { HistoryView } from "$/shared/integrations/inertia";
    import { normalizeIsoString } from "$/shared/lib/date/normalize-iso-string";
    import { dispatchInput } from "$/shared/lib/dom/dispatch-input";
    import { DeferUntilNextFrame } from "$/shared/lib/svelte/defer-until-next-frame.svelte";
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
        deferHistoryViewFrames?: number;
        beforeOpen?: () => void | boolean;
    };

    let {
        tooltip,
        notifyAt = $bindable(),
        startsAt,
        name,
        deferHistoryViewFrames = 0,
        beforeOpen
    }: Props = $props();

    const customPickerView = new HistoryView<{
        __selectreminder: { isOpen: boolean };
    }>();
    const deferredView = new DeferUntilNextFrame(() => deferHistoryViewFrames);

    let announcerInput = $state<HTMLInputElement | null>(null);
    let isMenuOpen = $state(false);

    async function onSelect(duration: string) {
        notifyAt = durationToZonedDT(startsAt, duration);
        await tick();
        dispatchInput(announcerInput);
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
        () =>
            deferredView.ready &&
            (customPickerView.meta?.__selectreminder?.isOpen ?? false),
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
