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
        on_abort?: VoidFunction;
        on_confirm?: (time: Time) => void;
    };

    let { value = $bindable(), trigger, on_abort, on_confirm, ...props }: Props = $props();

    let open = $state(false);

    const visible_value = $derived.by(() => {
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
        class={tw("rounded-md text-center font-bold", props.class)}
        onclick={() => (open = true)}
    >
        {visible_value}
    </button>
{/if}

<TimePickerClock bind:value bind:open {on_abort} {on_confirm} />
