<script lang="ts">
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { boolAttr } from "runed";

    import { Week } from "../model/week.svelte";

    import type { Dayjs } from "dayjs";

    type Props = {
        day: Dayjs;
    };

    let { day = $bindable() }: Props = $props();

    const week = new Week(() => day);
</script>

<div class="border-b border-cream-300 p-3 pb-5">
    <div class="flex h-10 items-stretch">
        <button
            onclick={() => (day = week.previous())}
            class="flex w-7 shrink-0 items-center justify-center text-xl text-cream-700"
        >
            <ChevronLeft />
        </button>
        <div class="grid w-full grid-cols-7">
            {#each week.days as d (d.day())}
                <button
                    onclick={() => (day = d)}
                    class="group flex flex-col items-center justify-between"
                    data-selected={boolAttr(day.isSame(d, "date"))}
                >
                    <span class="text-2xs text-cream-500" data-part="day-name">
                        {d.format("dd").charAt(0).toLocaleUpperCase(d.locale())}
                    </span>
                    <span
                        class="relative text-sm text-cream-800 group-data-selected:text-cream-950"
                        data-part="day-number"
                    >
                        {d.date()}
                    </span>
                </button>
            {/each}
        </div>
        <button
            onclick={() => (day = week.next())}
            class="flex w-7 shrink-0 items-center justify-center text-xl text-cream-700"
        >
            <ChevronRight />
        </button>
    </div>
</div>

<style>
    button[data-selected] [data-part="day-number"]::after {
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
