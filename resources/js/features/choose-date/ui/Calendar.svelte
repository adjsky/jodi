<script lang="ts">
    import { DateFormatter } from "@internationalized/date";
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { CalendarEvents } from "$/entities/event/api/calendar-events.svelte";
    import { YearCalendar } from "$/entities/event/model/year-calendar.svelte";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { tw } from "$/shared/lib/css/tw";
    import { Year } from "$/shared/lib/date/year.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import { toaster } from "$/shared/ui/toaster";

    import CalendarMonth from "./CalendarMonth.svelte";

    import type { CalendarMode } from "../model/types";
    import type { CalendarDate } from "@internationalized/date";
    import type { WeekStart } from "$/shared/lib/types";
    import type { Attachment } from "svelte/attachments";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["div"], "children" | "title"> & {
        portal?: boolean;
        mode: CalendarMode;
        selected: CalendarDate[];
        weekStart: WeekStart;
        min?: CalendarDate | null;
        getDateAttachment?: (
            date: CalendarDate
        ) => Attachment<HTMLButtonElement>;
        onClose?: VoidFunction;
        onSelect?: (date: CalendarDate[]) => void;
    };

    const {
        mode,
        selected,
        weekStart,
        min,
        getDateAttachment,
        onClose,
        onSelect,
        ...props
    }: Props = $props();

    let monthsNode = $state<HTMLElement | null>(null);

    $effect(() => {
        if (!monthsNode) return;

        const selectors = [
            'button[data-highlight="selected"]',
            'button[data-highlight="range-start"]',
            'button[data-highlight="today"]'
        ];

        for (const selector of selectors) {
            const element = monthsNode.querySelector(selector);

            if (element) {
                element.scrollIntoView({ block: "center" });
                break;
            }
        }
    });

    let draftSelected = $derived(selected);

    const year = new Year(
        () => selected[0],
        () => ({ weekStart, locale: getLocale() })
    );

    const calendar = new YearCalendar(() => ({
        weekStart,
        locale: getLocale()
    }));

    const events = new CalendarEvents({
        onSuccess(year, months, events) {
            calendar.prepare(year, months, events);
        },
        onError() {
            toaster.error(m["calendar.request-error"]());
        }
    });

    function gotoYear(direction: "next" | "previous") {
        year[direction]();
        monthsNode?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function gotoCurrentYear() {
        year.current = new Date().getFullYear();
        monthsNode?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function onMonthSelect(date: CalendarDate) {
        if (mode == "single") {
            onSelect?.([date]);
            return;
        }

        const [from, to] = draftSelected;

        if (from && to) {
            draftSelected = [date];
            return;
        }

        if (from && !to) {
            if (from.compare(date) > 0) {
                draftSelected = [date, from];
            } else {
                draftSelected = [from, date];
            }
        }
    }

    function format(date: CalendarDate) {
        return new DateFormatter(getLocale(), {
            day: "numeric",
            month: "short"
        }).format(date.toDate(TIMEZONE));
    }
</script>

<FloatingView {...props} class={tw(props.class, "pb-safe")}>
    {#snippet back()}
        <button class="p-2" type="button" onclick={onClose}>
            <ChevronLeft class="text-4xl" />
        </button>
    {/snippet}
    {#snippet action()}
        <div class="flex items-center gap-4 text-xl">
            <button
                type="button"
                class="text-2xl font-bold"
                onclick={gotoCurrentYear}
            >
                {year.current}
            </button>

            <div class="flex gap-2">
                <Button
                    type="button"
                    variant="secondary"
                    class="h-auto rounded-full bg-transparent p-2"
                    onclick={() => gotoYear("previous")}
                >
                    <ChevronLeft />
                </Button>
                <Button
                    type="button"
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
        {#each year.months() as month (`${month.name}-${year.current}`)}
            <CalendarMonth
                {...month}
                {year}
                {mode}
                {min}
                {calendar}
                selected={draftSelected}
                onSelect={onMonthSelect}
                container={monthsNode}
                attachment={getDateAttachment}
                onEventsRequest={(date) => events.request(date)}
            />
        {/each}
    </div>

    {#if mode == "range"}
        {@const [from, to] = selected}
        <Button
            class="my-4 mb-5 shrink-0"
            onclick={() => {
                onSelect?.(draftSelected);
            }}
        >
            {format(from)}

            {#if to && from.compare(to) != 0}
                - {format(to)}
            {/if}
        </Button>
    {/if}
</FloatingView>
