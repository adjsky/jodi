<script lang="ts">
    import { ParaglideMessage } from "@inlang/paraglide-js-svelte";
    import { DateFormatter, toCalendarDate } from "@internationalized/date";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import NumericInput from "$/shared/ui/NumericInput.svelte";

    import CustomConfiguratorBlock from "./CustomConfiguratorBlock.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        day: ZonedDateTime;
        limit: "never" | "until" | "count";
        until: Date;
        count: string;
    };

    let {
        day,
        limit = $bindable(),
        until = $bindable(),
        count = $bindable()
    }: Props = $props();
</script>

<CustomConfiguratorBlock title={m["recurrence.custom.select-when-to-end"]()}>
    <div class="mt-2">
        <Checkbox
            label={m["recurrence.custom.never"]()}
            checked={limit == "never"}
            onclick={() => {
                limit = "never";
            }}
        />
        <Checkbox
            checked={limit == "until"}
            onclick={() => {
                limit = "until";
            }}
        >
            {#snippet label()}
                <YearCalendarDialog
                    selected={toCalendarDate(day)}
                    id="recurrence-until-calendar"
                    onSelect={(date) => {
                        until = date.toDate("UTC");
                    }}
                >
                    {#snippet children(props)}
                        <div
                            {...props()}
                            class="flex h-10 items-center gap-1.5 rounded-md border border-cream-300 px-2 font-medium"
                        >
                            {new DateFormatter(getLocale(), {
                                day: "2-digit",
                                year: "numeric",
                                month: "short",
                                weekday: "short"
                            }).format(until ?? day.add({ months: 1 }).toDate())}
                        </div>
                    {/snippet}
                </YearCalendarDialog>
            {/snippet}
        </Checkbox>
        <div class="flex items-stretch">
            <Checkbox
                class="w-auto shrink-0 py-0"
                checked={limit == "count"}
                onclick={() => {
                    limit = "count";
                }}
            />
            <!-- svelte-ignore a11y_click_events_have_key_events -->
            <div
                role="button"
                tabindex="-1"
                class="w-full cursor-pointer ps-2"
                onclick={() => (limit = "count")}
            >
                <ParaglideMessage
                    message={m["recurrence.custom.end-after-times"]}
                    inputs={{ t: count }}
                >
                    {#snippet input()}
                        <NumericInput
                            bind:value={count}
                            min={0}
                            class="w-15 text-center"
                        />
                    {/snippet}
                </ParaglideMessage>
            </div>
        </div>
    </div>
</CustomConfiguratorBlock>
