<script lang="ts">
    import "imask/masked/range";

    import IMask from "imask/holder";
    import { watch } from "runed";
    import { tick } from "svelte";

    import { announce } from "../lib/form";
    import { tw } from "../lib/styles";
    import { isMobile } from "../lib/user-agent";

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

    let timeInput = $state<HTMLInputElement | null>(null);
    let mask = $state<InputMask | null>(null);

    async function oncomplete(v: string) {
        const [hour, minute] = v.split(":").map(Number);
        value = value?.set({ hour, minute });
        onComplete?.();

        await tick();

        if (timeInput) {
            timeInput.value = visibleValue;
            announce(timeInput);
        }
    }

    async function onpickerchange(e: { currentTarget: HTMLInputElement }) {
        void oncomplete(e.currentTarget.value);
        await tick();
        mask?.updateValue();
    }

    function onfocus(e: FocusEvent) {
        if (isMobile() && navigator.userActivation.isActive) {
            e.preventDefault();
            timeInput?.showPicker();
        }
    }

    function onblur() {
        if (!mask) return;

        if (!mask.masked.isComplete) {
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
                void oncomplete(mask.value);
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
    {onfocus}
    {onblur}
    {required}
/>

<input
    bind:this={timeInput}
    type="time"
    class="sr-only"
    onchange={onpickerchange}
    {name}
/>
