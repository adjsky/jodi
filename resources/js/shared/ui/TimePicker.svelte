<script lang="ts">
    import "imask/masked/range";

    import { m } from "$/paraglide/messages";
    import IMask from "imask/holder";
    import { watch } from "runed";
    import { tick } from "svelte";
    import TimepickerUI from "timepicker-ui";

    import { tw } from "../lib/styles";

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

    const visibleValue = $derived(
        value
            ? `${value.hour.toString().padStart(2, "0")}:${value.minute.toString().padStart(2, "0")}`
            : ""
    );

    let mask = $state<InputMask | null>(null);

    async function oncomplete(hour: number, minute: number) {
        value = value?.set({ hour, minute });
        onComplete?.();
    }

    function onblur() {
        if (mask && !mask.masked.isComplete) {
            mask.value = visibleValue;
        }
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
                void oncomplete(hour, minute);
            });

            return () => mask?.destroy();
        }
    );

    watch(
        () => [ref],
        () => {
            if (!ref) return;

            const picker = new TimepickerUI(ref, {
                ui: {
                    animation: true,
                    backdrop: true
                },
                clock: {
                    type: "24h"
                },
                labels: {
                    time: m["common.time-picker.title"](),
                    ok: m["common.time-picker.ok"](),
                    cancel: m["common.time-picker.cancel"]()
                },
                callbacks: {
                    async onConfirm({ hour, minutes }) {
                        void oncomplete(Number(hour), Number(minutes));
                        await tick();
                        mask?.updateValue();
                    }
                }
            });

            picker.create();

            return () => {
                picker.destroy();
            };
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
    {onblur}
    {required}
    {name}
/>
