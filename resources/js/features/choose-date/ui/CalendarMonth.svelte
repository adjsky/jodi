<script lang="ts">
    import { today } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { boolAttr, useIntersectionObserver } from "runed";

    import type { CalendarDate } from "@internationalized/date";
    import type { YearCalendar } from "$/entities/event/model/year-calendar.svelte";
    import type { Year } from "$/shared/lib/date/year.svelte";
    import type { Attachment } from "svelte/attachments";

    type Props = {
        selected: CalendarDate;
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
            {@const color = segment.color ?? "var(--color-brand)"}
            <div
                class={[
                    "h-4 truncate px-1 text-2xs leading-4 font-bold text-cream-950",
                    "data-ends-in-week:mr-1.5 data-ends-in-week:rounded-r-full data-starts-in-week:border-l-4"
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
    {@const disabled = min ? min.compare(date) > 0 : false}
    {@const isToday = today(TIMEZONE).compare(date) == 0}
    {@const isSelected = selected.compare(date) == 0}
    <td>
        <button
            {@attach attachment?.(date)}
            {disabled}
            type="button"
            class="group flex h-25 w-full flex-col items-center pt-1 text-lg disabled:cursor-not-allowed disabled:line-through disabled:opacity-40 data-outside-month:opacity-40"
            data-highlight={isToday
                ? "today"
                : isSelected
                  ? "selected"
                  : undefined}
            data-outside-month={boolAttr(!isWithinMonth)}
            onclick={() => onSelect?.(date)}
        >
            <span
                class={[
                    "relative inline-flex size-9 shrink-0 items-center justify-center rounded-full border-cream-950",
                    "group-data-[highlight=selected]:border group-data-[highlight=selected]:font-bold",
                    "group-data-[highlight=today]:bg-brand group-data-[highlight=today]:font-bold group-data-[highlight=today]:text-white"
                ]}
            >
                {date.day}
            </span>
        </button>
    </td>
{/snippet}
