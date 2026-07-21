<script lang="ts">
    import { HistoryView } from "../inertia/history-view.svelte";
    import { useDeferUntilNextFrame } from "../lib/hooks.svelte";
    import { tw } from "../lib/styles";
    import TimePickerClock from "./TimePickerClock.svelte";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        class?: ClassName;
        value: Time;
        deferHistoryViewFrames?: number;
        trigger?: Snippet<[HTMLButtonAttributes]>;
        onAbort?: VoidFunction;
        onConfirm?: (time: Time) => void;
    };

    let id = $props.id();
    let {
        class: classname,
        value = $bindable(),
        deferHistoryViewFrames = 0,
        trigger,
        onAbort,
        onConfirm
    }: Props = $props();

    const view = new HistoryView<{
        __timepickerinput: { isPickerOpen: string };
    }>();
    const deferredView = useDeferUntilNextFrame(() => deferHistoryViewFrames);

    const visibleValue = $derived.by(() => {
        const hours = value.hour.toString().padStart(2, "0");
        const minutes = value.minute.toString().padStart(2, "0");

        return hours + ":" + minutes;
    });

    function showPicker() {
        void view.push(view.name, {
            meta: {
                ...view.meta,
                __timepickerinput: { isPickerOpen: id }
            }
        });
    }
</script>

{#if trigger}
    {@render trigger({
        type: "button",
        onclick() {
            showPicker();
        }
    })}
{:else}
    <button
        type="button"
        class={tw("rounded-md text-center font-bold", classname)}
        onclick={() => showPicker()}
    >
        {visibleValue}
    </button>
{/if}

<TimePickerClock
    bind:value
    bind:open={
        () =>
            deferredView.ready &&
            view.meta?.__timepickerinput?.isPickerOpen == id,
        (v) => {
            if (v) {
                showPicker();
            } else {
                void view.back();
            }
        }
    }
    {onAbort}
    {onConfirm}
/>
