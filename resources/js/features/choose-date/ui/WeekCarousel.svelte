<script lang="ts">
    import { inertia } from "@inertiajs/svelte";
    import {
        DateFormatter,
        getDayOfWeek,
        today
    } from "@internationalized/date";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";

    import { useWeekSwiper } from "../model/use-week-swiper.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { WeekStart } from "$/shared/lib/types";
    import type { SwiperContainer } from "swiper/element";

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

    let swiperContainer: SwiperContainer | null = $state(null);

    const swiper = useWeekSwiper(
        () => swiperContainer,
        () => ({
            cursor,
            weekStart,
            onCursorChange: (date) => (cursor = date)
        })
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

<swiper-container
    bind:this={swiperContainer}
    init="false"
    class="border-b border-cream-300 py-3"
>
    {#each swiper.weeks as week, idx (idx)}
        <swiper-slide class="grid h-14 grid-cols-7 pb-2">
            {#each week as date (date.toString())}
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
        </swiper-slide>
    {/each}
</swiper-container>

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
