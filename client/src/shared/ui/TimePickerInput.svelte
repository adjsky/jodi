<script lang="ts">
    import { tw } from "../lib/styles/tw.ts";
    import TimePickerClock from "./TimePickerClock.svelte";

    import type { ClassName } from "../lib/styles/tw.ts";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        class?: ClassName;
        value: Time;
        trigger?: Snippet<[HTMLButtonAttributes]>;
        onAbort?: VoidFunction;
        onConfirm?: (time: Time) => void;
    };

    let {
        class: classname,
        value = $bindable(),
        trigger,
        onAbort,
        onConfirm
    }: Props = $props();

    let open = $state(false);

    const visibleValue = $derived.by(() => {
        const hours = value.hour.toString().padStart(2, "0");
        const minutes = value.minute.toString().padStart(2, "0");

        return hours + ":" + minutes;
    });
</script>

{#if trigger}
    {@render trigger({
        type: "button",
        onclick() {
            open = true;
        }
    })}
{:else}
    <button
        type="button"
        class={tw("rounded-md text-center font-bold", classname)}
        onclick={() => (open = true)}
    >
        {visibleValue}
    </button>
{/if}

<TimePickerClock bind:value bind:open {onAbort} {onConfirm} />
