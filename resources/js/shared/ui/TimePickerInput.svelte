<script lang="ts">
    import "imask/masked/range";

    import IMask from "imask/holder";
    import { watch } from "runed";
    import { tick } from "svelte";

    import { announce } from "../lib/form";
    import { tw } from "../lib/styles";
    import TimePickerClock from "./TimePickerClock.svelte";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";
    import type InputMask from "imask/esm/controls/input";

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
    let mask = $state<InputMask | null>(null);

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

    watch(
        () => [ref],
        () => {
            if (!ref) return;

            mask = IMask(ref, {
                overwrite: true,
                autofix: true,
                mask: "HH:MM",
                lazy: false,
                blocks: {
                    HH: {
                        mask: IMask.MaskedRange,
                        placeholderChar: "-",
                        from: 0,
                        to: 23,
                        maxLength: 2
                    },
                    MM: {
                        mask: IMask.MaskedRange,
                        placeholderChar: "-",
                        from: 0,
                        to: 59,
                        maxLength: 2
                    }
                }
            });

            mask.on("complete", () => {
                if (!mask) return;
                const [hour, minute] = mask.value.split(":").map(Number);
                oncomplete(hour, minute);
            });

            return () => mask?.destroy();
        }
    );
</script>

<input
    bind:this={ref}
    value={visibleValue}
    class={tw(
        "w-12 rounded-md text-center font-bold max-md:outline-none",
        classname
    )}
    type="text"
    onblur={() => {
        if (mask && !mask.masked.isComplete) {
            mask.value = visibleValue;
        }
    }}
    onclick={() => (isPickerOpen = true)}
    {required}
    {name}
/>

<TimePickerClock
    {value}
    bind:open={isPickerOpen}
    onComplete={async (t) => {
        oncomplete(t.hour, t.minute);
        await tick();
        announce(ref);
        mask?.updateValue();
    }}
/>
