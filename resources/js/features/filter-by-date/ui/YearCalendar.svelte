<script lang="ts">
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import { onMount } from "svelte";

    import { Year } from "../model/year.svelte";
    import Month from "./Month.svelte";

    import type { WeekStart } from "../cfg/preferences";
    import type { DateValue } from "@internationalized/date";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["div"], "children" | "title"> & {
        selected: DateValue;
        start: WeekStart;
        onClose?: VoidFunction;
        onSelect?: (date: DateValue) => void;
    };

    const { selected, start, onClose, onSelect, ...props }: Props = $props();

    let monthsNode = $state<HTMLElement | null>(null);

    onMount(() => {
        const selected = monthsNode?.querySelector("button[data-selected]");
        selected?.scrollIntoView({ block: "center" });
    });

    let year = $derived(new Year(selected, () => start));

    function gotoYear(direction: "next" | "previous") {
        year[direction]();
        monthsNode?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function gotoCurrentYear() {
        year.current = new Date().getFullYear();
        monthsNode?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }
</script>

<FloatingView {...props}>
    {#snippet back()}
        <button class="p-2" onclick={onClose}>
            <ChevronLeft class="text-4xl" />
        </button>
    {/snippet}
    {#snippet action()}
        <div class="flex items-center gap-4 text-xl">
            <button class="text-2xl font-bold" onclick={gotoCurrentYear}>
                {year.current}
            </button>

            <div class="flex gap-2">
                <Button
                    variant="secondary"
                    class="h-auto rounded-full bg-transparent p-2"
                    onclick={() => gotoYear("previous")}
                >
                    <ChevronLeft />
                </Button>
                <Button
                    variant="secondary"
                    class="h-auto rounded-full bg-transparent p-2"
                    onclick={() => gotoYear("next")}
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

    <div bind:this={monthsNode} class="mt-2 overflow-y-scroll">
        {#each year.months() as month (month.name)}
            <Month
                {...month}
                {year}
                {selected}
                {onSelect}
                container={monthsNode}
            />
        {/each}
    </div>
</FloatingView>
