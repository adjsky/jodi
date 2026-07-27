<script lang="ts">
    import { inertia } from "@inertiajs/svelte";
    import {
        DateFormatter,
        getDayOfWeek,
        today
    } from "@internationalized/date";
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { Week } from "$/shared/lib/date/week.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { WeekStart } from "$/shared/lib/types";

    type Props = {
        selected: CalendarDate;
        cursor: CalendarDate;
        weekStart: WeekStart;
    };

    let {
        selected = $bindable(),
        cursor = $bindable(),
        weekStart
    }: Props = $props();

    const week = new Week(
        () => cursor,
        () => ({ weekStart, locale: getLocale() })
    );

    function highlight(date: CalendarDate) {
        if (selected.compare(date) == 0) {
            return "selected";
        }

        if (today(TIMEZONE).compare(date) == 0) {
            return "today";
        }

        const locale = getLocale();

        if (getDayOfWeek(selected, locale) == getDayOfWeek(date, locale)) {
            return "ghost";
        }

        return null;
    }
</script>

<div class="border-b border-cream-300 p-3 pt-1 pb-5">
    <div class="flex h-12 items-stretch">
        <button
            onclick={() => (cursor = week.previous())}
            class="flex w-7 shrink-0 items-center justify-center text-2xl text-cream-700"
        >
            <ChevronLeft />
        </button>
        <div class="grid w-full grid-cols-7">
            {#each week.days as date (date.day)}
                <button
                    use:inertia={{
                        href: `?d=${date.toString()}`,
                        only: ["todos", "events"],
                        showProgress: true
                    }}
                    class="group flex flex-col items-center justify-between"
                    data-highlight={highlight(date)}
                >
                    <span
                        class="text-xs font-semibold text-cream-500"
                        data-part="day-name"
                    >
                        {new DateFormatter(getLocale(), {
                            weekday: "short"
                        }).format(date.toDate(TIMEZONE))}
                    </span>
                    <span
                        class="relative font-normal text-cream-800"
                        data-part="day-number"
                    >
                        {date.day}
                    </span>
                </button>
            {/each}
        </div>
        <button
            onclick={() => (cursor = week.next())}
            class="flex w-7 shrink-0 items-center justify-center text-2xl text-cream-700"
        >
            <ChevronRight />
        </button>
    </div>
</div>

<style>
    button:is([data-highlight]) [data-part="day-number"]::after {
        content: "";

        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);

        width: 2.25rem;
        height: 2.25rem;

        border-radius: 50%;
        z-index: -1;
    }

    button[data-highlight="today"] [data-part="day-number"] {
        color: var(--color-brand);
        font-weight: var(--font-weight-bold);

        &::after {
            border: 1px solid var(--color-brand);
        }
    }

    button[data-highlight="selected"] [data-part="day-number"] {
        color: var(--color-white);
        font-weight: var(--font-weight-bold);

        &::after {
            background: var(--color-brand);
        }
    }

    button[data-highlight="ghost"] [data-part="day-number"]::after {
        border: 1px dashed var(--color-cream-800);
    }
</style>
