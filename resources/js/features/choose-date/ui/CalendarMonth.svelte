<script lang="ts">
    import { today } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { boolAttr, useIntersectionObserver } from "runed";

    import type { CalendarMode } from "../model/types";
    import type { CalendarDate } from "@internationalized/date";
    import type { YearCalendar } from "$/entities/event/model/year-calendar.svelte";
    import type { Year } from "$/shared/lib/date/year.svelte";
    import type { Attachment } from "svelte/attachments";

    type Props = {
        mode: CalendarMode;
        selected: CalendarDate[];
        year: Year;
        date: CalendarDate;
        min?: CalendarDate | null;
        name: string;
        container: HTMLElement;
        calendar: YearCalendar;
        attachment?: (date: CalendarDate) => Attachment<HTMLButtonElement>;
        onSelect?: (date: CalendarDate) => void;
        onEventsRequest?: (date: CalendarDate) => void;
    };

    const {
        mode,
        selected,
        year,
        date,
        min,
        name,
        container,
        calendar,
        attachment,
        onSelect,
        onEventsRequest
    }: Props = $props();

    let table = $state<HTMLTableElement | null>(null);

    useIntersectionObserver(
        () => table,
        (entries) => {
            if (entries[0]?.isIntersecting) {
                void onEventsRequest?.(date);
            }
        },
        { root: () => container, threshold: 0, rootMargin: "100px 0px" }
    );

    function highlight(date: CalendarDate) {
        if (mode == "single") {
            if (selected[0].compare(date) == 0) {
                return "selected";
            }
        } else {
            const [from, to] = selected;

            if (!to || from.compare(to) == 0) {
                if (from.compare(date) == 0) {
                    return "selected";
                }
            } else {
                const diffFrom = from.compare(date);
                const diffTo = to.compare(date);

                if (diffFrom == 0) {
                    return "range-start";
                }

                if (diffTo == 0) {
                    return "range-end";
                }

                if (diffTo > 0 && diffFrom < 0) {
                    return "range-middle";
                }
            }
        }

        if (today(TIMEZONE).compare(date) == 0) {
            return "today";
        }

        return null;
    }
</script>

<table bind:this={table} class="w-full">
    <caption class="pb-1 text-right text-xl font-bold">
        {name}
    </caption>
    <tbody>
        {#each year.weeks(date) as week, weekIdx (weekIdx)}
            <tr class="relative grid grid-cols-7 border-t border-cream-200">
                {#each week as { date, isWithinMonth } (date.day)}
                    {@render day(date, isWithinMonth)}
                {/each}
                {@render events(
                    week.map(({ date }) => date),
                    weekIdx
                )}
            </tr>
        {/each}
    </tbody>
</table>

{#snippet events(week: CalendarDate[], weekIdx: number)}
    {@const segments = calendar.visibleSegments(
        date.year,
        date.month,
        weekIdx + 1
    )}
    <td
        class="pointer-events-none absolute inset-x-0 top-11 grid grid-cols-7 gap-y-0.5"
    >
        {#each segments as segment (segment.eventId)}
            {@const color = segment.color ?? "var(--color-tangerine)"}
            <div
                class={[
                    "h-4 truncate px-1 text-2xs leading-4 font-bold text-cream-950",
                    "data-ends-in-week:mr-1.5 data-ends-in-week:rounded-e-full data-starts-in-week:border-s-4"
                ]}
                style:grid-column="{segment.column} / span {segment.span}"
                style:grid-row={segment.lane + 1}
                style:background-color="color-mix(in srgb, {color} 15%, transparent)"
                style:border-color={color}
                data-starts-in-week={boolAttr(segment.startsInWeek)}
                data-ends-in-week={boolAttr(segment.endsInWeek)}
            >
                {segment.title}
            </div>
        {/each}
        {#each week as date, columnIdx (date.day)}
            {@const count = calendar.overflow(
                date.year,
                date.month,
                weekIdx + 1,
                columnIdx + 1
            )}
            {#if count > 0}
                <span
                    class="text-center text-2xs font-bold text-cream-500"
                    style:grid-column={columnIdx + 1}
                >
                    {m["calendar.more"]({ count })}
                </span>
            {/if}
        {/each}
    </td>
{/snippet}

{#snippet day(date: CalendarDate, isWithinMonth: boolean)}
    {@const highlighted = highlight(date)}
    <td>
        <button
            {@attach attachment?.(date)}
            disabled={min ? min.compare(date) > 0 : false}
            type="button"
            class="group relative flex h-25 w-full flex-col items-center pt-1 text-lg disabled:cursor-not-allowed disabled:line-through disabled:opacity-40 data-outside-month:opacity-60"
            data-highlight={highlighted}
            data-outside-month={boolAttr(!isWithinMonth)}
            onclick={() => onSelect?.(date)}
        >
            <span
                class={[
                    "inline-flex size-9 shrink-0 items-center justify-center rounded-full border-cream-950",
                    "group-data-[highlight=selected]:bg-brand group-data-[highlight=selected]:font-bold group-data-[highlight=selected]:text-white",
                    "group-data-[highlight=range-start]:bg-brand group-data-[highlight=range-start]:font-bold group-data-[highlight=range-start]:text-white",
                    "group-data-[highlight=range-end]:bg-brand group-data-[highlight=range-end]:font-bold group-data-[highlight=range-end]:text-white",
                    "group-data-[highlight=today]:border group-data-[highlight=today]:border-brand group-data-[highlight=today]:font-bold group-data-[highlight=today]:text-brand"
                ]}
            >
                {date.day}
            </span>
            {#if highlighted == "range-start" || highlighted == "range-middle" || highlighted == "range-end"}
                <span
                    class={[
                        "pointer-events-none absolute inset-x-0 top-0.5 h-10 border-y border-dashed border-brand",
                        highlighted == "range-start" &&
                            "left-1 rounded-s-full border-s",
                        highlighted == "range-end" &&
                            "right-1 rounded-e-full border-e"
                    ]}
                ></span>
            {/if}
        </button>
    </td>
{/snippet}
