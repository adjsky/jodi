<script lang="ts">
    import { Circle } from "@lucide/svelte";
    import { home } from "$/generated/routes";
    import { link } from "$/shared/inertia/link";
    import { noop } from "$/shared/lib/function";
    import { boolAttr, useIntersectionObserver } from "runed";

    import { requestSummary, summaryCache } from "../api/day-summary.svelte";
    import { compareDates } from "../helpers/date";

    import type { Year } from "../model/year.svelte";
    import type { DateValue } from "@internationalized/date";

    type Props = {
        selected: DateValue;
        year: Year;
        date: DateValue;
        name: string;
        container: HTMLElement;
        onSelect?: (date: DateValue) => void;
    };

    const { selected, year, date, name, container, onSelect }: Props = $props();

    let table = $state<HTMLTableElement | null>(null);

    useIntersectionObserver(
        () => table,
        (entries) => {
            if (entries[0]?.isIntersecting) {
                void requestSummary(date);
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
        {#each year.weeks(date) as week, idx (idx)}
            <tr class="grid grid-cols-7 border-t border-cream-200">
                {#each week as { date, isWithinMonth } (date.day)}
                    {@render day(date, isWithinMonth)}
                {/each}
            </tr>
        {/each}
    </tbody>
</table>

{#snippet day(date: DateValue, isWithinMonth: boolean)}
    {@const summary = summaryCache.get(date.year)?.get(date.toString())}
    {@const inertia = onSelect ? (noop as never) : link}
    <td class:invisible={!isWithinMonth}>
        <button
            {@attach inertia(() => ({
                href: home({ query: { d: date.toString() } }),
                viewTransition: true
            }))}
            class="group flex h-22 w-full flex-col items-center pt-1 text-lg"
            data-selected={boolAttr(compareDates(selected, date) == "selected")}
            onclick={() => onSelect?.(date)}
        >
            <span
                class={[
                    "relative inline-flex size-9 shrink-0 items-center justify-center rounded-full",
                    "group-data-selected:bg-brand group-data-selected:font-bold group-data-selected:text-white"
                ]}
            >
                {date.day}
            </span>
            {#if summary?.events}
                {#each summary.events as event (event)}
                    <span
                        class="mt-0.5 inline-flex gap-1 rounded-full px-0.5 py-px text-2xs font-bold"
                        style="background: {event.color ?? 'transparent'}"
                    >
                        <Circle class="fill-[white]" />
                        <span class="leading-none" data-part="event-title">
                            {event.title.slice(0, 5)}
                        </span>
                    </span>
                {/each}
            {/if}
            {#if summary?.nMore}
                <span
                    class="mt-0.5 text-2xs leading-3 font-bold text-cream-500"
                >
                    +{summary.nMore}
                </span>
            {/if}
        </button>
    </td>
{/snippet}

<style>
    [data-part="event-title"] {
        mask-image: linear-gradient(
            to right,
            var(--color-cream-950) 80%,
            transparent 100%
        );
    }
</style>
