<script lang="ts">
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { home } from "$/generated/routes";
    import { link } from "$/shared/inertia/link";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import { boolAttr } from "runed";
    import { onMount } from "svelte";

    import { compareDates } from "../helpers/date";
    import { Year } from "../model/year.svelte";

    import type { WeekStart } from "../cfg/preferences";
    import type { Month } from "../model/year.svelte";
    import type { CalendarDate } from "@internationalized/date";

    type Props = {
        selected: CalendarDate;
        start: WeekStart;
    };

    const { selected, start }: Props = $props();

    let monthsContainer = $state<HTMLElement | null>(null);

    const year = $derived(new Year(selected, () => start));

    function goToYear(direction: "next" | "previous") {
        year[direction]();
        monthsContainer?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    onMount(() => {
        monthsContainer
            ?.querySelector("button[data-selected]")
            ?.scrollIntoView();
    });
</script>

<FloatingView back={home()} viewTransition>
    {#snippet action()}
        <div class="flex items-center gap-4 text-xl">
            <span class="text-2xl font-bold">{year.current}</span>

            <div class="flex gap-2">
                <Button
                    variant="secondary"
                    class="h-auto rounded-full p-2"
                    onclick={() => goToYear("previous")}
                >
                    <ChevronLeft />
                </Button>
                <Button
                    variant="secondary"
                    class="h-auto rounded-full p-2"
                    onclick={() => goToYear("next")}
                >
                    <ChevronRight />
                </Button>
            </div>
        </div>
    {/snippet}

    <div class="mt-1 grid grid-cols-7 text-xs font-semibold">
        {#each year.weekdays() as weekday, idx (idx)}
            <span class="text-center">{weekday}</span>
        {/each}
    </div>

    <div bind:this={monthsContainer} class="mt-2 overflow-y-scroll pb-12">
        {#each year.months() as month (month.name)}
            {@render table(month)}
        {/each}
    </div>
</FloatingView>

{#snippet table(month: Month)}
    <table class="w-full">
        <caption class="pb-1 text-right text-xl font-bold">
            {month.name}
        </caption>
        <tbody>
            {#each year.weeks(month) as week, idx (idx)}
                <tr class="grid grid-cols-7 border-t border-cream-200">
                    {#each week as { date, isWithinMonth } (date.day)}
                        <td>
                            <button
                                {@attach link(() => ({
                                    href: home({
                                        query: { d: date.toString() }
                                    }),
                                    viewTransition: true,
                                    replace: true
                                }))}
                                class={[
                                    "relative flex w-full items-center justify-center pt-2 pb-8 text-lg data-selected:font-bold data-selected:text-white",
                                    !isWithinMonth && "hidden"
                                ]}
                                data-selected={boolAttr(
                                    compareDates(selected, date) == "selected"
                                )}
                            >
                                {date.day}
                            </button>
                        </td>
                    {/each}
                </tr>
            {/each}
        </tbody>
    </table>
{/snippet}

<style>
    button[data-selected]::after {
        content: "";

        position: absolute;
        left: 50%;
        transform: translateX(-50%);

        width: 2.25rem;
        height: 2.25rem;

        border-radius: 50%;
        z-index: -1;
        background: var(--color-brand);
    }
</style>
