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
    import { useDragSelection } from "$/shared/lib/hooks/use-drag-selection";
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
        attachment?: (date: CalendarDate) => Attachment<HTMLButtonElement>;
        onClose?: VoidFunction;
        onSelect?: (date: CalendarDate[]) => void;
    };

    const {
        mode,
        selected,
        weekStart,
        min,
        attachment,
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

    const dragSelection = useDragSelection<CalendarDate>(() => ({
        enabled: mode == "range",
        isSelectable(value) {
            if (!min) {
                return true;
            }

            return min.compare(value) <= 0;
        },
        onSelect(date, anchor) {
            if (date.compare(anchor) > 0) {
                draftSelected = [anchor, date];
            } else {
                draftSelected = [date, anchor];
            }
        }
    }));

    function gotoYear(direction: "next" | "previous") {
        year[direction]();
        monthsNode?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function gotoCurrentYear() {
        year.current = new Date().getFullYear();
        monthsNode?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function onPress(date: CalendarDate) {
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

    function formatSelected([from, to]: CalendarDate[]) {
        const f = new DateFormatter(getLocale(), {
            day: "numeric",
            month: "short"
        });

        if (to && from.compare(to) != 0) {
            return `${f.format(from.toDate(TIMEZONE))} - ${f.format(to.toDate(TIMEZONE))}`;
        }

        return f.format(from.toDate(TIMEZONE));
    }
</script>

<FloatingView {...props} class={tw(props.class, "pb-safe")}>
    {#snippet back()}
        <button class="-ms-2 p-2" type="button" onclick={onClose}>
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

    <div class="mt-1 week-grid-9 text-xs font-semibold">
        {#each year.weekdays() as weekday, idx (idx)}
            <span
                class={tw([
                    "inline-flex min-w-0 justify-center",
                    idx == 0 && "justify-start",
                    idx == 6 && "justify-end"
                ])}
            >
                <span class="inline-flex w-full max-w-9 justify-center">
                    {weekday}
                </span>
            </span>
        {/each}
    </div>

    <div bind:this={monthsNode} class="-mx-4 mt-2 overflow-y-scroll">
        {#each year.months() as month (`${month.name}-${year.current}`)}
            <CalendarMonth
                {...month}
                {year}
                {mode}
                {min}
                {calendar}
                {onPress}
                attachments={{
                    dateButton: attachment,
                    dragSelection: dragSelection.attachment
                }}
                selected={draftSelected}
                container={monthsNode}
                onEventsRequest={(date) => events.request(date)}
            />
        {/each}
    </div>

    {#if mode == "range"}
        <Button
            class="my-4 mb-5 shrink-0"
            onclick={() => {
                const [from, to] = draftSelected;
                onSelect?.([from, to ?? from]);
            }}
        >
            {formatSelected(draftSelected)}
        </Button>
    {/if}
</FloatingView>
