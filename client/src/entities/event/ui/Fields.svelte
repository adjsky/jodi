<script lang="ts">
    import { toTime } from "@internationalized/date";
    import { Calendar, Clock } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { DEFER_FRAMES } from "$/shared/cfg/constants";
    import { get_random_int } from "$/shared/lib/random/get-random-int";
    import TimeRangePicker from "$/shared/ui/TimeRangePicker.svelte";

    import { format_date_range } from "../helpers/format-date-range";

    import type { Time, ZonedDateTime } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        starts_at: ZonedDateTime;
        ends_at: ZonedDateTime;
        title?: string;
        description?: string | null;
        calendar: Snippet<[Snippet<[HTMLButtonAttributes]>]>;
        close: Snippet;
        destroy: Snippet;
        repeat: Snippet;
        color: Snippet;
        notify: Snippet;
        more: Snippet;
        on_starts_at_change?: (time: Time) => void;
        on_ends_at_change?: (time: Time) => void;
    };

    let {
        starts_at = $bindable(),
        ends_at = $bindable(),
        title,
        description,
        calendar,
        close,
        destroy,
        repeat,
        color,
        notify,
        more,
        on_starts_at_change,
        on_ends_at_change
    }: Props = $props();

    const title_suggestions = [
        m["events.placeholders.title.second-date"](),
        m["events.placeholders.title.games-night"](),
        m["events.placeholders.title.beach-day"](),
        m["events.placeholders.title.hot-yoga"](),
        m["events.placeholders.title.karaoke"](),
        m["events.placeholders.title.disco-night"](),
        m["events.placeholders.title.spain-trip"](),
        m["events.placeholders.title.park-picnic"](),
        m["events.placeholders.title.cutting-bangs"](),
        m["events.placeholders.title.dentist"](),
        m["events.placeholders.title.self-care-sunday"](),
        m["events.placeholders.title.leg-day"](),
        m["events.placeholders.title.croissant-mission"](),
        m["events.placeholders.title.important-nap"](),
        m["events.placeholders.title.coffee-emergency"](),
        m["events.placeholders.title.cancel-plans"](),
        m["events.placeholders.title.sunset-chasing"](),
        m["events.placeholders.title.survive-monday"]()
    ];

    const title_placeholder = title_suggestions[get_random_int(0, title_suggestions.length - 1)];
</script>

<div class="flex items-center justify-between">
    {#snippet trigger(props: HTMLButtonAttributes)}
        <button {...props} class="flex items-center gap-1.5 text-lg font-bold" type="button">
            <Calendar />
            {format_date_range(starts_at, ends_at)}
        </button>
    {/snippet}
    {@render calendar(trigger)}
    {@render close()}
</div>

<div class="flex min-h-0 grow flex-col overflow-y-auto overscroll-contain pb-keyboard-or-18">
    <input
        class="mt-5 form-input w-full border-none bg-transparent p-0 text-xl font-bold text-cream-950 placeholder:text-cream-600 focus:ring-0"
        placeholder={title_placeholder}
        name="title"
        defaultValue={title ?? ""}
        maxlength={120}
        required
        data-expand-sheet
    />

    <TimeRangePicker
        bind:starts_at={() => toTime(starts_at), (time) => (starts_at = starts_at.set(time))}
        bind:ends_at={() => toTime(ends_at), (time) => (ends_at = ends_at.set(time))}
        {on_starts_at_change}
        {on_ends_at_change}
        class="mt-4"
        defer_history_view_frames={DEFER_FRAMES.SHEET + 1}
        is_valid={starts_at.compare(ends_at) < 0}
    >
        {#snippet label()}
            <Clock class="text-2xl" />
            <span class="font-bold">{m["events.time"]()}</span>
        {/snippet}
    </TimeRangePicker>

    <textarea
        name="description"
        placeholder={m["events.placeholders.description"]()}
        class="mt-3 form-input min-h-34 w-full flex-1 resize-none overflow-y-auto overscroll-contain border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
        defaultValue={description ?? ""}
        maxlength={2000}
        data-expand-sheet></textarea>
</div>

<div
    class="absolute inset-x-0 bottom-0 flex items-end justify-between rounded-t-2xl bg-white px-4 pb-safe-offset-6"
>
    {@render destroy()}
    {@render repeat()}
    {@render color()}
    {@render notify()}
    {@render more()}
</div>
