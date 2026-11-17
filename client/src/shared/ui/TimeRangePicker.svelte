<script lang="ts">
    import { TriangleAlert } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";

    import { tw } from "../lib/styles/tw.ts";
    import TimePickerInput from "./TimePickerInput.svelte";

    import type { ClassName } from "../lib/styles/tw.ts";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";

    type Props = {
        label: Snippet;
        class?: ClassName;
        starts_at: Time;
        ends_at: Time;
        is_valid?: boolean;
        on_starts_at_change?: (time: Time) => void;
        on_ends_at_change?: (time: Time) => void;
    };

    let {
        label,
        starts_at = $bindable(),
        ends_at = $bindable(),
        is_valid,
        on_starts_at_change,
        on_ends_at_change,
        ...props
    }: Props = $props();
</script>

<div class={tw("group flex w-full items-center gap-4 text-lg", props.class)}>
    <span class="flex items-center gap-1.5 select-none">
        {@render label?.()}
    </span>
    <div
        class={[
            "flex h-10 w-max items-center rounded-lg border border-cream-300 bg-white px-4 select-none",
            !is_valid && "border-red"
        ]}
    >
        <TimePickerInput
            bind:value={starts_at}
            on_confirm={(time) => {
                if (time.hour === 23) {
                    ends_at = time.set({ hour: 23, minute: 59 });
                } else {
                    ends_at = time.set({
                        hour: time.hour + 1,
                        minute: time.minute
                    });
                }

                on_starts_at_change?.(time);
            }}
        />
        <div aria-hidden="true" class="px-1.5 text-md font-semibold text-cream-600">
            {m["common.to"]()}
        </div>
        <TimePickerInput bind:value={ends_at} on_confirm={on_ends_at_change} />
        {#if !is_valid}
            <TriangleAlert class="ml-2 text-2xl text-red" />
        {/if}
    </div>
</div>
