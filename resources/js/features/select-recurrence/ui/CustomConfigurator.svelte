<script lang="ts">
    import { createListCollection, Select } from "@ark-ui/svelte";
    import { page } from "@inertiajs/svelte";
    import { DateFormatter, now, startOfWeek } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import {
        TIMEZONE,
        WEEK_START_PREFERENCE_MAP
    } from "$/shared/cfg/constants";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import NumericInput from "$/shared/ui/NumericInput.svelte";
    import PromptDialog from "$/shared/ui/PromptDialog.svelte";
    import { Frequency, RRule, rrulestr } from "rrule";
    import { boolAttr } from "runed";

    import {
        RRULE_TO_SELECTABLE_FREQUENCY_MAP,
        SELECTABLE_TO_RRULE_FREQUENCY_MAP,
        SelectableFrequency
    } from "../cfg/frequency";
    import { dateToWeekday, getOrderedWeekdays } from "../helpers/weekday";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        open: boolean;
        day: ZonedDateTime;
        rrule?: string | null;
        onSelect?: (rrule: string) => void;
    };

    let { open = $bindable(), day, rrule, onSelect }: Props = $props();

    const DEFAULT_INTERVAL = "1";
    const DEFAULT_FREQ = Frequency.WEEKLY;
    const DEFAULT_BYWEEKDAY = $derived([dateToWeekday(day.toDate())]);
    const DEFAULT_COUNT = null;
    const DEFAULT_UNTIL = null;

    const user = $derived($page.props.auth.user);
    const rule = $derived(rrule ? rrulestr(rrule) : null);

    let interval = $derived(
        rule?.options.interval.toString() ?? DEFAULT_INTERVAL
    );
    let freq = $derived(rule?.options.freq ?? DEFAULT_FREQ);
    let byweekday = $derived(rule?.options.byweekday ?? DEFAULT_BYWEEKDAY);
    let count = $derived(rule?.options.count ?? DEFAULT_COUNT);
    let until = $derived(rule?.options.until ?? DEFAULT_UNTIL);

    const intervals = $derived(
        createListCollection({
            items: [
                {
                    label: m["common.intervals.days"]({ a: interval }),
                    value: SelectableFrequency.DAILY
                },
                {
                    label: m["common.intervals.weeks"]({ a: interval }),
                    value: SelectableFrequency.WEEKLY
                },
                {
                    label: m["common.intervals.months"]({ a: interval }),
                    value: SelectableFrequency.MONTHLY
                },
                {
                    label: m["common.intervals.years"]({ a: interval }),
                    value: SelectableFrequency.YEARLY
                }
            ]
        })
    );
</script>

<PromptDialog
    bind:open
    title={m["recurrence.custom.title"]()}
    label={{
        abort: m["recurrence.custom.cancel"](),
        confirm: m["recurrence.custom.ok"]()
    }}
    disabled={Number(interval) === 0}
    onConfirm={() => {
        onSelect?.(
            new RRule({
                freq,
                interval: Number(interval),
                byweekday
            }).toString()
        );
        open = false;
    }}
    onExitComplete={() => {
        interval = DEFAULT_INTERVAL;
        freq = DEFAULT_FREQ;
        byweekday = DEFAULT_BYWEEKDAY;
        count = DEFAULT_COUNT;
        until = DEFAULT_UNTIL;
    }}
>
    <div class="mt-4 flex gap-2">
        <NumericInput bind:value={interval} class="w-20" min={0} />

        <Select.Root
            bind:value={
                () => [RRULE_TO_SELECTABLE_FREQUENCY_MAP[freq]],
                ([f]) => (freq = SELECTABLE_TO_RRULE_FREQUENCY_MAP[f])
            }
            onSelect={({ value }) => {
                if (value == SelectableFrequency.WEEKLY) {
                    byweekday = [dateToWeekday(day.toDate())];
                } else {
                    byweekday = [];
                }
            }}
            collection={intervals}
            multiple={false}
        >
            <Select.Control
                class="relative flex h-full items-center gap-2 rounded-xl bg-cream-500/10"
            >
                <Select.Trigger
                    class="flex h-full items-center ps-4 pe-7 text-lg font-medium"
                >
                    <Select.ValueText />
                </Select.Trigger>
                <Select.Indicator
                    class="triangle pointer-events-none absolute right-3"
                />
            </Select.Control>
            <Select.Positioner>
                <Select.Content
                    class="min-w-30 rounded-xl border border-cream-950 bg-white"
                >
                    {#each intervals.items as item (item.value)}
                        <Select.Item
                            {item}
                            class="cursor-pointer border-cream-300 px-4 py-3 not-last:border-b"
                        >
                            <Select.ItemText>{item.label}</Select.ItemText>
                            <Select.ItemIndicator />
                        </Select.Item>
                    {/each}
                </Select.Content>
            </Select.Positioner>
            <Select.HiddenSelect />
        </Select.Root>
    </div>

    {#if freq == RRule.WEEKLY}
        {@const start = startOfWeek(
            now(TIMEZONE),
            getLocale(),
            WEEK_START_PREFERENCE_MAP[user.preferences.weekStartOn]
        )}
        <hr class="mt-4 h-px text-cream-300" />
        <h3 class="mt-4 text-sm font-semibold">
            {m["recurrence.custom.select-weekdays"]()}
        </h3>
        <div class="mt-4 flex gap-1.5">
            {#each getOrderedWeekdays(start) as { weekday, date } (weekday)}
                {@const selected = byweekday.includes(weekday)}
                {@const formatter = new DateFormatter(getLocale(), {
                    weekday: "narrow"
                })}
                <button
                    class="size-8.5 shrink-0 rounded-full border border-cream-300 data-selected:border-brand data-selected:bg-brand data-selected:font-semibold data-selected:text-white"
                    onclick={() => {
                        if (selected) {
                            byweekday = byweekday.filter((w) => weekday != w);
                        } else {
                            byweekday = [...byweekday, weekday];
                        }
                    }}
                    data-selected={boolAttr(selected)}
                >
                    {formatter.format(date)}
                </button>
            {/each}
        </div>
    {/if}

    <hr class="mt-4 h-px text-cream-300" />
    <h3 class="mt-4 text-sm font-semibold">
        {m["recurrence.custom.select-when-to-end"]()}
    </h3>
    <div class="mt-3">
        <Checkbox
            label={m["recurrence.custom.never"]()}
            checked={count === null && until === null}
            onclick={() => {
                count = null;
                until = null;
            }}
        />
        <Checkbox
            label={m["recurrence.custom.never"]()}
            checked={count === null && until === null}
            onclick={() => {
                count = null;
                until = null;
            }}
        />
    </div>
</PromptDialog>
