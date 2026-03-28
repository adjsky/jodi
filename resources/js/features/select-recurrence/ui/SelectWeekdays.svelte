<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { DateFormatter, now, startOfWeek } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import {
        TIMEZONE,
        WEEK_START_PREFERENCE_MAP
    } from "$/shared/cfg/constants";
    import { boolAttr } from "runed";

    import { getOrderedWeekdays } from "../helpers/weekday";
    import CustomConfiguratorBlock from "./CustomConfiguratorBlock.svelte";

    type Props = {
        byweekday: number[];
    };

    let { byweekday = $bindable() }: Props = $props();

    const user = $derived($page.props.auth.user);
    const start = $derived(
        startOfWeek(
            now(TIMEZONE),
            getLocale(),
            WEEK_START_PREFERENCE_MAP[user.preferences.weekStartOn]
        )
    );
</script>

<CustomConfiguratorBlock title={m["recurrence.custom.select-weekdays"]()}>
    <div class="mt-3 flex gap-1.5">
        {#each getOrderedWeekdays(start) as { weekday, date } (weekday)}
            {@const selected = byweekday.includes(weekday)}
            {@const formatter = new DateFormatter(getLocale(), {
                weekday: "narrow"
            })}
            <button
                type="button"
                class="size-10 shrink-0 rounded-full border border-cream-300 data-selected:border-brand data-selected:bg-brand data-selected:font-semibold data-selected:text-white"
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
</CustomConfiguratorBlock>
