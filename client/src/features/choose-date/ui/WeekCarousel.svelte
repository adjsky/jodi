<script lang="ts">
    import { DateFormatter, getDayOfWeek, today } from "@internationalized/date";
    import { getLocale } from "#paraglide/runtime.js";
    import { TIMEZONE } from "#shared/cfg/constants.ts";
    import { tw } from "#shared/lib/styles/tw.ts";

    import { SelectedDate } from "../model/selected-date.svelte.ts";
    import { WeekSwiper } from "../model/week-swiper.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { WeekStart } from "#shared/lib/date/types.ts";
    import type { SwiperContainer } from "swiper/element";

    type Props = {
        selected: CalendarDate;
        cursor: CalendarDate;
        week_start: WeekStart;
    };

    let { selected = $bindable(), cursor = $bindable(), week_start }: Props = $props();

    let swiper_container: SwiperContainer | null = $state(null);

    const swiper = new WeekSwiper(
        () => swiper_container,
        () => ({
            cursor,
            week_start: week_start,
            on_cursor_change: (date) => (cursor = date)
        })
    );

    function highlight(date: CalendarDate) {
        if (selected.compare(date) === 0) {
            return "selected";
        }

        if (today(TIMEZONE).compare(date) === 0) {
            return "today";
        }

        const locale = getLocale();

        if (getDayOfWeek(selected, locale) === getDayOfWeek(date, locale)) {
            return "ghost";
        }

        return null;
    }
</script>

<swiper-container bind:this={swiper_container} init="false" class="border-b border-cream-300 py-3">
    {#each swiper.weeks as week, week_idx (week_idx)}
        <swiper-slide class="week-grid-12.5 h-14 pb-2">
            {#each week as date, column_idx (column_idx)}
                <button
                    data-highlight={highlight(date)}
                    class={tw([
                        "flex min-w-0 justify-center",
                        column_idx === 0 && "justify-start",
                        column_idx === 6 && "justify-end"
                    ])}
                    onclick={() => SelectedDate.update(date)}
                >
                    <span
                        class="flex h-full w-full max-w-12.5 flex-col items-center justify-between"
                    >
                        <span class="text-xs font-semibold text-cream-500" data-part="day-name">
                            {new DateFormatter(getLocale(), {
                                weekday: "short",
                                timeZone: TIMEZONE
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
