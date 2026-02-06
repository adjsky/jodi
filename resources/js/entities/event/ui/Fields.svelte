<script lang="ts">
    import { DateFormatter, toTime } from "@internationalized/date";
    import { Calendar, Clock } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import TimeRangePicker from "$/shared/ui/TimeRangePicker.svelte";
    import { boolAttr } from "runed";

    import type { Time, ZonedDateTime } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        startsAt: ZonedDateTime;
        endsAt: ZonedDateTime;
        title?: string;
        description?: string | null;
        calendar: Snippet<[Snippet<[HTMLButtonAttributes]>]>;
        close: Snippet;
        destroy: Snippet;
        repeat: Snippet;
        color: Snippet;
        notify: Snippet;
        more: Snippet;
        onStartsAtChange?: (time: Time) => void;
    };

    let {
        startsAt = $bindable(),
        endsAt = $bindable(),
        title,
        description,
        calendar,
        close,
        destroy,
        repeat,
        color,
        notify,
        more,
        onStartsAtChange
    }: Props = $props();
</script>

<div class="flex items-center justify-between">
    <!-- DO NOT REMOVE DIV -->
    <div>
        {#snippet trigger(props: HTMLButtonAttributes)}
            <button
                {...props}
                class="flex items-center gap-1.5 text-lg font-bold"
                type="button"
            >
                <Calendar />
                {new DateFormatter(getLocale(), {
                    day: "2-digit",
                    year: "numeric",
                    month: "short",
                    weekday: "short"
                }).format(startsAt.toDate())}
            </button>
        {/snippet}
        {@render calendar(trigger)}
    </div>
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

<TimeRangePicker
    bind:startsAt={
        () => toTime(startsAt), (time) => (startsAt = startsAt.set(time))
    }
    bind:endsAt={() => toTime(endsAt), (time) => (endsAt = endsAt.set(time))}
    {onStartsAtChange}
    class="mt-4"
    name="_time"
    required
>
    {#snippet label()}
        <Clock class="text-2xl" />
        <span class="font-bold">{m["events.time"]()}</span>
    {/snippet}
</TimeRangePicker>

<textarea
    name="description"
    placeholder={m["events.placeholders.description"]()}
    class="mt-3 form-input field-sizing-content w-full grow overflow-y-scroll border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    defaultValue={description ?? ""}
></textarea>

<div
    class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between bg-white px-4 pb-safe-offset-6"
>
    {@render destroy()}
    {@render repeat()}
    {@render color()}
    {@render notify()}
    {@render more()}
</div>
