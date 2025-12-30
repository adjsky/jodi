<script lang="ts">
    import { m } from "$/paraglide/messages";
    import { TimeRangeField } from "bits-ui";

    import { tw } from "../lib/styles";

    import type { ClassName } from "../lib/styles";
    import type { Time } from "@internationalized/date";
    import type { Snippet } from "svelte";

    type Props = {
        label: Snippet;
        locale: string;
        class?: ClassName;
        name?: string;
        required?: boolean;
        startsAt?: Time;
        endsAt?: Time;
    };

    let {
        label,
        locale,
        class: classname,
        name,
        required,
        startsAt = $bindable(),
        endsAt = $bindable()
    }: Props = $props();

    const types = ["start", "end"] as const;

    let root: HTMLElement | null = $state(null);

    function announce(start?: Time, end?: Time) {
        if (!root) {
            return;
        }

        const times = [start, end];

        for (let i = 0; i < types.length; i++) {
            const type = types[i];
            const time = times[i];

            if (!time) {
                continue;
            }

            const timeInputName = name + "_" + type;
            const input = root.querySelector(`input[name="${timeInputName}"]`);

            input?.dispatchEvent(new Event("input", { bubbles: true }));
        }
    }

    function checkValidity(start?: Time, end?: Time) {
        if (!start || !root) {
            return;
        }

        const input = root.querySelector<HTMLInputElement>(
            `input[name="${name}_end"]`
        );
        if (!input) return;

        if (!end) {
            return input.setCustomValidity(m["common.required-field"]());
        }

        if (start.compare(end) < 0) {
            return input.setCustomValidity("");
        }

        input.setCustomValidity(m["common.invalid-time-range"]());
    }
</script>

<TimeRangeField.Root
    {locale}
    {required}
    bind:ref={root}
    bind:value={
        () => ({ start: startsAt, end: endsAt }),
        (v) => {
            startsAt = v.start;
            endsAt = v.end;
            announce(v.start, v.end);
            checkValidity(v.start, v.end);
        }
    }
    class={tw("group flex w-full gap-4 text-lg", classname)}
    hourCycle={24}
    hideTimeZone
>
    <TimeRangeField.Label class="flex items-center gap-1.5 select-none">
        {@render label?.()}
    </TimeRangeField.Label>
    <div
        class="flex w-min items-center rounded-lg border border-cream-300 bg-white px-4 py-1.25 select-none"
    >
        {#each types as type (type)}
            <TimeRangeField.Input
                {type}
                name={name ? `${name}_${type}` : undefined}
            >
                {#snippet children({ segments })}
                    {#each segments as { part, value }, idx (idx)}
                        {#if part == "literal"}
                            <TimeRangeField.Segment
                                {part}
                                class="px-0.5 text-md font-semibold text-cream-600"
                                hidden={value == " "}
                            >
                                {value}
                            </TimeRangeField.Segment>
                        {:else}
                            <TimeRangeField.Segment
                                {part}
                                class="rounded-md px-0.5 font-bold outline-offset-0"
                            >
                                {value}
                            </TimeRangeField.Segment>
                        {/if}
                    {/each}
                {/snippet}
            </TimeRangeField.Input>
            {#if type === "start"}
                <div
                    aria-hidden="true"
                    class="px-1.5 text-md font-semibold text-cream-600"
                >
                    {m["common.to"]()}
                </div>
            {/if}
        {/each}
    </div>
</TimeRangeField.Root>
