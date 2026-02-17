<script lang="ts">
    import { tick } from "svelte";

    import { HistoryView } from "../inertia/history-view.svelte";
    import { announce } from "../lib/form";
    import { tw } from "../lib/styles";
    import { DISABLE_SHEET_DRAGGING } from "./Sheet.svelte";
    import TimePickerClock from "./TimePickerClock.svelte";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        ref?: HTMLInputElement | null;
        class?: ClassName;
        name: string;
        value?: Time;
        required?: boolean;
        trigger?: Snippet<[HTMLButtonAttributes]>;
        onAbort?: VoidFunction;
        onComplete?: (time: Time) => void;
    };

    let {
        ref = $bindable(null),
        class: classname,
        name,
        value = $bindable(),
        required,
        trigger,
        onAbort,
        onComplete
    }: Props = $props();

    const view = new HistoryView<{
        [DISABLE_SHEET_DRAGGING]: boolean;
        __timepickerinput: { isPickerOpen: string };
    }>();

    const visibleValue = $derived.by(() => {
        if (!value) {
            return "";
        }

        const hours = value.hour.toString().padStart(2, "0");
        const minutes = value.minute.toString().padStart(2, "0");

        return hours + ":" + minutes;
    });

    function oncomplete(time: Time) {
        value = value?.set(time);
        onComplete?.(time);
    }

    function showPicker() {
        void view.push(view.name, {
            ...view.meta,
            [DISABLE_SHEET_DRAGGING]: true,
            __timepickerinput: { isPickerOpen: name }
        });
    }
</script>

<input bind:this={ref} {name} {required} value={visibleValue} class="sr-only" />

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
        class={tw("w-12 rounded-md text-center font-bold", classname)}
        onclick={() => showPicker()}
    >
        {visibleValue}
    </button>
{/if}

<TimePickerClock
    {value}
    {onAbort}
    bind:open={
        () => view.meta?.__timepickerinput?.isPickerOpen == name,
        (v) => {
            if (v) {
                showPicker();
            } else {
                void view.back();
            }
        }
    }
    onComplete={async (t) => {
        oncomplete(t);
        await tick();
        announce(ref);
    }}
/>
