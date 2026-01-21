<script lang="ts">
    import { m } from "$/paraglide/messages";

    import { tw } from "../lib/styles";
    import TimePickerInput from "./TimePickerInput.svelte";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";

    type Props = {
        label: Snippet;
        class?: ClassName;
        startsAt?: Time;
        endsAt?: Time;
        name?: string;
        required?: boolean;
    };

    let {
        label,
        class: classname,
        name,
        startsAt = $bindable(),
        endsAt = $bindable(),
        required
    }: Props = $props();

    let endsAtInput = $state<HTMLInputElement | null>(null);

    function onStartsAtComplete() {
        if (!startsAt || !endsAt) return;

        endsAt = endsAt.set({
            hour: startsAt.hour + 1,
            minute: startsAt.minute
        });
    }

    function onEndsAtComplete() {
        if (!startsAt || !endsAtInput) {
            return;
        }

        if (!endsAt) {
            return endsAtInput.setCustomValidity(m["common.required-field"]());
        }

        if (startsAt.compare(endsAt) < 0) {
            return endsAtInput.setCustomValidity("");
        }

        endsAtInput.setCustomValidity(m["common.invalid-time-range"]());
    }
</script>

<div class={tw("group flex w-full gap-4 text-lg", classname)}>
    <span class="flex items-center gap-1.5 select-none">
        {@render label?.()}
    </span>
    <div
        class="flex w-min items-center rounded-lg border border-cream-300 bg-white px-4 py-1.25 select-none"
    >
        <TimePickerInput
            bind:value={startsAt}
            {required}
            name={name ? name + "_start" : ""}
            onComplete={onStartsAtComplete}
        />
        <div
            aria-hidden="true"
            class="px-1.5 text-md font-semibold text-cream-600"
        >
            {m["common.to"]()}
        </div>
        <TimePickerInput
            bind:ref={endsAtInput}
            bind:value={endsAt}
            {required}
            name={name ? name + "_end" : ""}
            onComplete={onEndsAtComplete}
        />
    </div>
</div>
