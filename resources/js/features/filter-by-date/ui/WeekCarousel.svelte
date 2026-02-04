<script lang="ts">
    import { DateFormatter } from "@internationalized/date";
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { boolAttr } from "runed";

    import { compareDates } from "../helpers/date";
    import { Week } from "../model/week.svelte";

    import type { WeekStart } from "../cfg/preferences";
    import type { CalendarDate } from "@internationalized/date";

    type Props = {
        selected: CalendarDate;
        cursor: CalendarDate;
        start: WeekStart;
    };

    let {
        selected = $bindable(),
        cursor = $bindable(),
        start
    }: Props = $props();

    const week = new Week(
        () => cursor,
        () => start
    );
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
                {@const compare = compareDates(selected, date)}
                <button
                    onclick={() => (selected = date)}
                    class="group flex flex-col items-center justify-between"
                    data-selected={boolAttr(compare == "selected")}
                    data-selected-ghost={boolAttr(compare == "ghost")}
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
                        class="relative font-semibold text-cream-800 group-data-selected:text-lg group-data-selected:text-white"
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
    button:is([data-selected], [data-selected-ghost])
        [data-part="day-number"]::after {
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

    button[data-selected] [data-part="day-number"]::after {
        background: var(--color-brand);
    }

    button[data-selected-ghost] [data-part="day-number"]::after {
        border: 1px dashed var(--color-cream-400);
    }
</style>
