<script lang="ts">
    import { inertia } from "@inertiajs/svelte";
    import {
        DateFormatter,
        getDayOfWeek,
        today
    } from "@internationalized/date";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { tw } from "$/shared/lib/styles/tw";

    import { WeekSwiper } from "../model/week-swiper.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { WeekStart } from "$/shared/lib/date/types";
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

    const swiper = new WeekSwiper(
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
    {#each swiper.weeks as week, weekIdx (weekIdx)}
        <swiper-slide class="week-grid-12.5 h-14 pb-2">
            {#each week as date, columnIdx (date.toString())}
                <button
                    use:inertia={{
                        href: `?d=${date.toString()}`,
                        only: ["todos", "events"],
                        showProgress: true
                    }}
                    data-highlight={highlight(date)}
                    class={tw([
                        "flex min-w-0 justify-center",
                        columnIdx == 0 && "justify-start",
                        columnIdx == 6 && "justify-end"
                    ])}
                >
                    <span
                        class="flex h-full w-full max-w-12.5 flex-col items-center justify-between"
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
                            class="relative text-lg font-normal text-cream-800"
                            data-part="day-number"
                        >
                            {date.day}
                        </span>
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
