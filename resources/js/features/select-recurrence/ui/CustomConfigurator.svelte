<script lang="ts">
    import {
        fromDate,
        Time,
        toCalendarDate,
        toCalendarDateTime
    } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import Button from "$/shared/ui/Button.svelte";
    import SheetDialog from "$/shared/ui/SheetDialog.svelte";
    import { Frequency, RRule, rrulestr } from "rrule";

    import { dateToWeekday } from "../helpers/weekday";
    import SelectFrequency from "./SelectFrequency.svelte";
    import SelectRecurrenceLimit from "./SelectRecurrenceLimit.svelte";
    import SelectWeekdays from "./SelectWeekdays.svelte";

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
    const DEFAULT_COUNT = "1";
    const DEFAULT_UNTIL = $derived(toCalendarDate(day).add({ months: 1 }));

    let rule = $derived(rrule ? rrulestr(rrule) : null);

    let { interval, freq, byweekday, count, until, limit } =
        $derived(getParts());

    function reset() {
        ({ interval, freq, byweekday, count, until, limit } = getParts());
    }

    function getParts() {
        return {
            interval: rule?.options.interval.toString() ?? DEFAULT_INTERVAL,
            freq: rule?.options.freq ?? DEFAULT_FREQ,
            byweekday: rule?.options.byweekday ?? DEFAULT_BYWEEKDAY,
            count: rule?.options.count?.toString() ?? DEFAULT_COUNT,
            until: rule?.options.until
                ? toCalendarDate(fromDate(rule.options.until, "UTC"))
                : DEFAULT_UNTIL,
            limit: rule?.options.count
                ? ("count" as const)
                : rule?.options.until
                  ? ("until" as const)
                  : ("never" as const)
        };
    }

    function onApply() {
        onSelect?.(
            new RRule({
                freq,
                interval: Number(interval),
                ...(freq == Frequency.WEEKLY && { byweekday }),
                ...(limit == "until" && {
                    until: toCalendarDateTime(
                        until,
                        new Time(23, 59, 59)
                    ).toDate("UTC")
                }),
                ...(limit == "count" && { count: Number(count) })
            }).toString()
        );
        open = false;
    }
</script>

<SheetDialog
    bind:open
    onExitComplete={reset}
    height={90}
    title={m["recurrence.custom.title"]()}
    portal
    lazyMount
>
    <SelectFrequency {day} bind:freq bind:interval bind:byweekday />

    {#if freq == RRule.WEEKLY}
        <SelectWeekdays bind:byweekday />
    {/if}

    <SelectRecurrenceLimit {day} bind:limit bind:until bind:count />

    <div class="flex grow items-end pb-5">
        <Button
            type="button"
            disabled={Number(interval) === 0 ||
                (limit === "count" && Number(count) === 0)}
            onclick={onApply}
        >
            {m["recurrence.custom.apply"]()}
        </Button>
    </div>
</SheetDialog>
