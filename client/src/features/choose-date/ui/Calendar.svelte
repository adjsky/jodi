<script lang="ts">
    import { DateFormatter } from "@internationalized/date";
    import { ChevronLeft, ChevronRight } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";
    import { getLocale } from "#paraglide/runtime.js";
    import { Haptics } from "#shared/capabilities/haptics/mod.ts";
    import { TIMEZONE } from "#shared/cfg/constants.ts";
    import { scroll_into_view } from "#shared/lib/dom/scroll-into-view.ts";
    import { use_drag_selection } from "#shared/lib/interaction/use-drag-selection.ts";
    import { tw } from "#shared/lib/styles/tw.ts";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import { toaster } from "#shared/ui/toaster.ts";

    import { Year } from "../model/year.svelte.ts";
    import CalendarMonth from "./CalendarMonth.svelte";

    import type { CalendarMode } from "../model/types";
    import type { CalendarDate } from "@internationalized/date";
    import type { WeekStart } from "#shared/lib/date/types.ts";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["div"], "children" | "title"> & {
        open: boolean;
        portal?: boolean;
        confirm: "manual" | "select";
        mode: CalendarMode;
        selected: CalendarDate[];
        week_start: WeekStart;
        min?: CalendarDate | null;
        on_select?: (date: CalendarDate[]) => void;
    };

    let {
        open = $bindable(),
        confirm,
        mode,
        selected,
        week_start,
        min,
        on_select,
        ...props
    }: Props = $props();

    let months_node = $state<HTMLElement | null>(null);

    $effect(() => {
        if (!months_node) return;

        const selectors = [
            'button[data-highlight="selected"]',
            'button[data-highlight="range-start"]',
            'button[data-highlight="today"]'
        ];

        for (const selector of selectors) {
            const element = months_node.querySelector(selector);

            if (element) {
                scroll_into_view(element, { block: "center" });
                break;
            }
        }
    });

    let draft_selected = $derived(selected);

    const year = new Year(
        () => selected[0],
        () => ({ week_start, locale: getLocale() })
    );

    // const calendar = new YearCalendar(() => ({
    //     weekStart,
    //     locale: getLocale()
    // }));

    // const events = new CalendarEvents({
    //     onSuccess(year, months, events) {
    //         calendar.prepare(year, months, events);
    //     },
    //     onError() {
    //         toaster.error(m["calendar.request-error"]());
    //     }
    // });

    const drag_selection = use_drag_selection<CalendarDate>(() => ({
        enabled: mode === "range",
        is_selectable(value) {
            if (!min) {
                return true;
            }

            return min.compare(value) <= 0;
        },
        on_start() {
            void Haptics.selection_start();
        },
        on_select(date, anchor, phase) {
            if (phase === "start") {
                void Haptics.impact("medium");
            } else {
                void Haptics.selection_changed();
            }

            if (date.compare(anchor) > 0) {
                draft_selected = [anchor, date];
            } else {
                draft_selected = [date, anchor];
            }
        },
        on_end() {
            void Haptics.selection_end();
        }
    }));

    function goto_year(direction: "next" | "previous") {
        year[direction]();
        months_node?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function goto_current_year() {
        year.current = new Date().getFullYear();
        months_node?.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }

    function on_press(date: CalendarDate) {
        if (confirm === "select") {
            on_select?.(get_next_selection(selected, date));
        } else {
            draft_selected = get_next_selection(draft_selected, date);
        }
    }

    function get_next_selection(current: CalendarDate[], date: CalendarDate) {
        if (mode === "single") {
            return [date];
        }

        const [from, to] = current;

        if (from && to) {
            return [date];
        }

        if (from && !to) {
            return from.compare(date) > 0 ? [date, from] : [from, date];
        }

        return [from, to];
    }

    function format_selected([from, to]: CalendarDate[]) {
        const f = new DateFormatter(getLocale(), {
            day: "numeric",
            month: "short",
            timeZone: TIMEZONE
        });

        if (to && from.compare(to) !== 0) {
            return `${f.format(from.toDate(TIMEZONE))} - ${f.format(to.toDate(TIMEZONE))}`;
        }

        return f.format(from.toDate(TIMEZONE));
    }
</script>

<ScreenView.Overlay bind:open {...props} class={tw(props.class, "pb-0")}>
    <ScreenView.Header shape="flat">
        {#snippet action()}
            <div class="flex items-center gap-4 text-xl">
                <button type="button" class="text-2xl font-bold" onclick={goto_current_year}>
                    {year.current}
                </button>

                <div class="flex gap-2">
                    <Button
                        type="button"
                        variant="secondary"
                        class="h-auto rounded-full bg-transparent p-2"
                        onclick={() => goto_year("previous")}
                    >
                        <ChevronLeft />
                    </Button>
                    <Button
                        type="button"
                        variant="secondary"
                        class="h-auto rounded-full bg-transparent p-2"
                        onclick={() => goto_year("next")}
                    >
                        <ChevronRight />
                    </Button>
                </div>
            </div>
        {/snippet}
    </ScreenView.Header>
    <ScreenView.Content class="mt-1">
        <div class="week-grid-9 text-xs font-semibold">
            {#each year.weekdays() as weekday, idx (idx)}
                <span
                    class={tw([
                        "inline-flex min-w-0 justify-center",
                        idx === 0 && "justify-start",
                        idx === 6 && "justify-end"
                    ])}
                >
                    <span class="inline-flex w-full max-w-9 justify-center">
                        {weekday}
                    </span>
                </span>
            {/each}
        </div>
        <div
            bind:this={months_node}
            class={[
                "-mx-4 mt-2 min-h-0 grow overflow-y-scroll overscroll-contain",
                mode === "single" && "pb-safe"
            ]}
        >
            {#each year.months() as month (`${month.name}-${year.current}`)}
                <CalendarMonth
                    {...month}
                    {year}
                    {mode}
                    {min}
                    {drag_selection}
                    {on_press}
                    selected={draft_selected}
                    container={months_node}
                />
            {/each}
        </div>
    </ScreenView.Content>

    {#if confirm === "manual"}
        <ScreenView.Footer class="mt-4 mb-safe-offset-5">
            <Button
                type="button"
                onclick={() => {
                    if (mode === "range") {
                        const [from, to] = draft_selected;
                        on_select?.([from, to ?? from]);
                    } else {
                        const [date] = draft_selected;
                        on_select?.([date]);
                    }
                }}
            >
                {format_selected(draft_selected)}
            </Button>
        </ScreenView.Footer>
    {/if}
</ScreenView.Overlay>
