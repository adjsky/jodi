<script lang="ts">
    import { createListCollection, Select } from "@ark-ui/svelte";
    import { m } from "$/paraglide/messages";
    import NumericInput from "$/shared/ui/NumericInput.svelte";

    import {
        RRULE_TO_SELECTABLE_FREQUENCY_MAP,
        SELECTABLE_TO_RRULE_FREQUENCY_MAP,
        SelectableFrequency
    } from "../cfg/frequency";
    import { dateToWeekday } from "../helpers/weekday";
    import CustomConfiguratorBlock from "./CustomConfiguratorBlock.svelte";

    import type { ZonedDateTime } from "@internationalized/date";
    import type { Frequency } from "rrule";

    type Props = {
        day: ZonedDateTime;
        freq: Frequency;
        interval: string;
        byweekday: number[];
    };

    let {
        day,
        freq = $bindable(),
        interval = $bindable(),
        byweekday = $bindable()
    }: Props = $props();

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

<CustomConfiguratorBlock title={m["recurrence.custom.select-frequency"]()}>
    <div class="mt-3 flex gap-2">
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
                            <Select.ItemText>
                                {item.label}
                            </Select.ItemText>
                            <Select.ItemIndicator />
                        </Select.Item>
                    {/each}
                </Select.Content>
            </Select.Positioner>
            <Select.HiddenSelect />
        </Select.Root>
    </div>
</CustomConfiguratorBlock>
