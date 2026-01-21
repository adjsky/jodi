<script lang="ts">
    import "imask/masked/range";

    import { tick } from "svelte";

    import { announce } from "../lib/form";
    import { tw } from "../lib/styles";
    import TimePickerClock from "./TimePickerClock.svelte";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";

    type Props = {
        ref?: HTMLInputElement | null;
        class?: ClassName;
        name?: string;
        value?: Time;
        required?: boolean;
        onComplete?: VoidFunction;
    };

    let {
        ref = $bindable(null),
        class: classname,
        name,
        value = $bindable(),
        required,
        onComplete
    }: Props = $props();

    let isPickerOpen = $state(false);

    const visibleValue = $derived.by(() => {
        if (!value) {
            return "";
        }

        const hours = value.hour.toString().padStart(2, "0");
        const minutes = value.minute.toString().padStart(2, "0");

        return hours + ":" + minutes;
    });

    function oncomplete(hour: number, minute: number) {
        value = value?.set({ hour, minute });
        onComplete?.();
    }
</script>

<input bind:this={ref} {name} {required} value={visibleValue} class="sr-only" />

<button
    type="button"
    class={tw("w-12 rounded-md text-center font-bold", classname)}
    onclick={() => (isPickerOpen = true)}
>
    {visibleValue}
</button>

<TimePickerClock
    {value}
    bind:open={isPickerOpen}
    onComplete={async (t) => {
        oncomplete(t.hour, t.minute);
        await tick();
        announce(ref);
    }}
/>
