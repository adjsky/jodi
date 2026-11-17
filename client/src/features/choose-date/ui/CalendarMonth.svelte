<script lang="ts">
    import { today } from "@internationalized/date";
    import { m } from "#paraglide/messages.js";
    import { Haptics } from "#shared/capabilities/haptics/mod.ts";
    import { TIMEZONE } from "#shared/cfg/constants.ts";
    import { tw } from "#shared/lib/styles/tw.ts";
    import { boolAttr, useIntersectionObserver } from "runed";

    import type { CalendarMode } from "../model/types";
    import type { Year } from "../model/year.svelte";
    import type { CalendarDate } from "@internationalized/date";
    import type { DragSelection } from "#shared/lib/interaction/use-drag-selection.ts";
    import type { Attachment } from "svelte/attachments";

    type Props = {
        mode: CalendarMode;
        selected: CalendarDate[];
        year: Year;
        date: CalendarDate;
        min?: CalendarDate | null;
        name: string;
        container: HTMLElement;
        // calendar: YearCalendar;
        drag_selection: DragSelection<CalendarDate>;
        on_press?: (date: CalendarDate) => void;
        // onEventsRequest?: (date: CalendarDate) => void;
    };

    const { mode, selected, year, date, min, name, container, drag_selection, on_press }: Props =
        $props();

    let table = $state<HTMLTableElement | null>(null);

    // useIntersectionObserver(
    //     () => table,
    //     (entries) => {
    //         if (entries[0]?.isIntersecting) {
    //             void onEventsRequest?.(date);
    //         }
    //     },
    //     { root: () => container, threshold: 0, rootMargin: "100px 0px" }
    // );

    function get_highlight(date: CalendarDate) {
        const [from, to] = selected;

        if (mode === "single" || !to || from.compare(to) === 0) {
            if (from.compare(date) === 0) {
                return "selected";
            }
        }

        if (today(TIMEZONE).compare(date) === 0) {
            return "today";
        }

        return null;
    }

    function get_range_position(date: CalendarDate) {
        if (mode === "single") {
            return null;
        }

        const [from, to] = selected;

        if (!to || from.compare(to) === 0) {
            return null;
        }

        const diff_from = from.compare(date);
        const diff_to = to.compare(date);

        if (diff_from === 0) {
            return "start";
        }

        if (diff_to === 0) {
            return "end";
        }

        if (diff_to > 0 && diff_from < 0) {
            return "middle";
        }

        return null;
    }
</script>

<table bind:this={table} class="w-full">
    <caption class="px-4 pb-1 text-right text-xl font-bold">
        {name}
    </caption>
    <tbody>
        {#each year.weeks(date) as week, week_idx (week_idx)}
            <tr class="relative week-grid-17 border-t border-cream-200">
                {#each week as { date, is_within_month }, column_idx (date.day)}
                    {@render day(date, is_within_month, column_idx)}
                {/each}
                <!-- {@render events(
                    week.map(({ date }) => date),
                    week_idx
                )} -->
            </tr>
        {/each}
    </tbody>
</table>
<!--
{#snippet events(week: CalendarDate[], week_idx: number)}
    {@const segments = calendar.visible_segments(
        date.year,
        date.month,
        week_idx + 1
    )}
    <td class="event-week-grid pointer-events-none">
        {#each segments as segment (segment.id)}
            {@const color = segment.color ?? "var(--color-tangerine)"}
            <div
                class={[
                    "mx-px h-4 truncate px-0.5 text-2xs leading-4 font-bold tracking-tight text-cream-950",
                    "not-data-starts-in-week:ps-1.25 data-ends-in-week:rounded-e-full data-starts-in-week:border-s-3"
                ]}
                style:grid-column="{segment.column} / span {segment.span}"
                style:grid-row={segment.lane + 1}
                style:background-color="color-mix(in srgb, {color} 15%, transparent)"
                style:border-color={color}
                data-starts-in-week={boolAttr(segment.starts_in_week)}
                data-ends-in-week={boolAttr(segment.ends_in_week)}
            >
                {segment.title}
            </div>
        {/each}
        {#each week as weekday, column_idx (weekday.day)}
            {@const count = calendar.overflow(
                date.year,
                date.month,
                week_idx + 1,
                column_idx + 1
            )}
            {#if count > 0}
                <span
                    class="text-center text-2xs font-bold text-cream-500"
                    style:grid-column={column_idx + 1}
                >
                    {m["calendar.more"]({ count })}
                </span>
            {/if}
        {/each}
    </td>
{/snippet} -->

{#snippet day(date: CalendarDate, is_within_month: boolean, column_idx: number)}
    {@const range_position = get_range_position(date)}
    <td>
        <button
            {@attach drag_selection.attachment(date)}
            disabled={min ? min.compare(date) > 0 : false}
            type="button"
            class={tw([
                "group relative flex h-25 w-full flex-col items-center pt-1 text-lg disabled:cursor-not-allowed disabled:line-through disabled:opacity-40 data-outside-month:opacity-60",
                column_idx === 0 && "items-start ps-4",
                column_idx === 6 && "items-end pe-4"
            ])}
            data-highlight={get_highlight(date)}
            data-range={range_position}
            data-outside-month={boolAttr(!is_within_month)}
            onclick={() => on_press?.(date)}
        >
            <span
                class={[
                    "inline-flex size-9 shrink-0 items-center justify-center rounded-full border-cream-950",
                    "group-data-[highlight=selected]:bg-brand group-data-[highlight=selected]:font-bold group-data-[highlight=selected]:text-white",
                    "group-data-[highlight=today]:border group-data-[highlight=today]:border-brand group-data-[highlight=today]:font-bold group-data-[highlight=today]:text-brand",
                    "group-data-[range=start]:bg-brand group-data-[range=start]:font-bold group-data-[range=start]:text-white",
                    "group-data-[range=end]:bg-brand group-data-[range=end]:font-bold group-data-[range=end]:text-white"
                ]}
            >
                {date.day}
            </span>
            {#if range_position !== null}
                <span
                    class={[
                        "pointer-events-none absolute -inset-x-1 top-0.5 h-10 border-y border-brand",
                        range_position === "start" &&
                            "left-[calc(50%-1.25rem)] rounded-s-full border-s",
                        range_position === "end" &&
                            "right-[calc(50%-1.25rem)] rounded-e-full border-e"
                    ]}
                ></span>
            {/if}
        </button>
    </td>
{/snippet}

<style lang="postcss">
    @reference "#app.css";

    .event-week-grid {
        --edge-center: 2.125rem;
        --day-width: calc((100% - 2 * var(--edge-center)) / 6);

        position: absolute;
        top: 2.75rem;
        left: calc(var(--edge-center) - var(--day-width) / 2);

        width: calc(7 * var(--day-width));

        display: grid;
        row-gap: 0.125rem;
        grid-template-columns: repeat(7, minmax(0, 1fr));

        @media (width > 29.75rem) {
            @apply week-grid-15;

            width: auto;
            inset-inline: 0.25rem;
        }
    }
</style>
