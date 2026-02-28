<script lang="ts">
    import { TriangleAlert } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";

    import { tw } from "../lib/styles";
    import TimePickerInput from "./TimePickerInput.svelte";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";

    type Props = {
        label: Snippet;
        class?: ClassName;
        startsAt: Time;
        endsAt: Time;
        onStartsAtChange?: (time: Time) => void;
        onEndsAtChange?: (time: Time) => void;
    };

    let {
        label,
        class: classname,
        startsAt = $bindable(),
        endsAt = $bindable(),
        onStartsAtChange,
        onEndsAtChange
    }: Props = $props();

    const isValid = $derived(startsAt.compare(endsAt) < 0);
</script>

<div class={tw("group flex w-full items-center gap-4 text-lg", classname)}>
    <span class="flex items-center gap-1.5 select-none">
        {@render label?.()}
    </span>
    <div
        class={[
            "flex h-10 w-max items-center rounded-lg border border-cream-300 bg-white px-4 select-none",
            !isValid && "border-red"
        ]}
    >
        <TimePickerInput
            bind:value={startsAt}
            onComplete={(time) => {
                if (time.hour == 23) {
                    endsAt = time.set({ hour: 23, minute: 59 });
                } else {
                    endsAt = time.set({
                        hour: time.hour + 1,
                        minute: time.minute
                    });
                }

                onStartsAtChange?.(time);
            }}
        />
        <div
            aria-hidden="true"
            class="px-1.5 text-md font-semibold text-cream-600"
        >
            {m["common.to"]()}
        </div>
        <TimePickerInput bind:value={endsAt} onComplete={onEndsAtChange} />
        {#if !isValid}
            <TriangleAlert class="ml-2 text-2xl text-red" />
        {/if}
    </div>
</div>
