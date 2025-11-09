<script lang="ts">
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { boolAttr } from "runed";

    import type { Calendar } from "../helpers/calendar.svelte";

    type Props = {
        calendar: Calendar;
    };

    const { calendar }: Props = $props();
</script>

<div class="border-b border-cream-300 p-3 pb-5">
    <div class="flex h-10 items-stretch">
        <button
            onclick={() => calendar.previousWeek()}
            class="flex w-7 shrink-0 items-center justify-center text-xl text-cream-700"
        >
            <ChevronLeft />
        </button>
        <div class="grid w-full grid-cols-7">
            {#each calendar.weekDays as day (day.date())}
                <button
                    onclick={() => (calendar.pointer = day)}
                    class="group flex flex-col items-center justify-between"
                    data-selected={boolAttr(day.isSame(calendar.pointer))}
                >
                    <span class="text-2xs text-cream-500">
                        {day
                            .format("dd")
                            .charAt(0)
                            .toLocaleUpperCase(day.locale())}
                    </span>
                    <span
                        class="relative text-sm text-cream-800 group-data-selected:text-cream-950"
                    >
                        {day.date()}
                    </span>
                </button>
            {/each}
        </div>
        <button
            onclick={() => calendar.nextWeek()}
            class="flex w-7 shrink-0 items-center justify-center text-xl text-cream-700"
        >
            <ChevronRight />
        </button>
    </div>
</div>

<style>
    button[data-selected] > :last-child::after {
        content: "";

        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);

        width: 2rem;
        height: 2rem;

        background: var(--color-brand);
        border-radius: 50%;
        z-index: -1;
    }
</style>
