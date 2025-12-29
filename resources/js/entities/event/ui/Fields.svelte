<script lang="ts">
    import { Field } from "@ark-ui/svelte";
    import { DateFormatter } from "@internationalized/date";
    import { Calendar, Clock } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { TimeRangeField } from "bits-ui";
    import { boolAttr } from "runed";

    import type { DateValue } from "@internationalized/date";
    import type { Snippet } from "svelte";

    type Props = {
        startsAt: DateValue;
        title?: string;
        description?: string | null;
        close: Snippet;
        destroy: Snippet;
        repeat: Snippet;
        color: Snippet;
        notify: Snippet;
        more: Snippet;
        onCalendarOpen: VoidFunction;
    };

    const {
        startsAt,
        title,
        description,
        close,
        destroy,
        repeat,
        color,
        notify,
        more,
        onCalendarOpen
    }: Props = $props();
</script>

<div class="flex items-center justify-between">
    <button
        class="flex items-center gap-1.5 text-lg font-bold"
        onclick={onCalendarOpen}
        type="button"
    >
        <Calendar />
        {new DateFormatter(getLocale(), {
            day: "2-digit",
            year: "numeric",
            month: "short",
            weekday: "short"
        }).format(startsAt.toDate(TIMEZONE))}
    </button>
    {@render close()}
</div>

<input
    class="mt-5 form-input w-full border-none bg-transparent p-0 text-xl font-bold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    placeholder={m["events.placeholders.title"]()}
    name="title"
    defaultValue={title ?? ""}
    required
    data-autofocus={boolAttr(!title)}
/>

<TimeRangeField.Root
    class="group mt-3 flex w-full gap-4 text-lg"
    locale={getLocale()}
    hourCycle={24}
    hideTimeZone
>
    <TimeRangeField.Label class="flex items-center gap-1.5 select-none">
        <Clock class="text-2xl" />
        <span class="font-bold">{m["events.time"]()}</span>
    </TimeRangeField.Label>
    <div
        class="flex w-min items-center rounded-lg border border-cream-300 bg-white px-4 py-1.25 select-none"
    >
        {#each ["start", "end"] as const as type (type)}
            <TimeRangeField.Input {type}>
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
                                class="rounded-md px-0.5 font-bold"
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
                    до
                </div>
            {/if}
        {/each}
    </div>
</TimeRangeField.Root>

<Field.Textarea
    autoresize
    name="description"
    placeholder={m["events.placeholders.description"]()}
    class="mt-6 form-input field-sizing-content w-full border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    defaultValue={description ?? ""}
/>

<div
    class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between bg-white px-4 pb-6"
>
    {@render destroy()}
    {@render repeat()}
    {@render color()}
    {@render notify()}
    {@render more()}
</div>
