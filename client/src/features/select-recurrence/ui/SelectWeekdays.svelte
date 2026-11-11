<script lang="ts">
    import { DateFormatter, now, startOfWeek } from "@internationalized/date";
    import { SessionCache } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { getLocale } from "#paraglide/runtime.js";
    import { TIMEZONE, WEEK_START_MAP } from "#shared/cfg/constants.ts";
    import { boolAttr } from "runed";

    import { getOrderedWeekdays } from "../helpers/weekday";
    import CustomConfiguratorBlock from "./CustomConfiguratorBlock.svelte";

    type Props = {
        byweekday: number[];
    };

    let { byweekday = $bindable() }: Props = $props();

    const session = SessionCache.retain();

    const start = $derived(
        startOfWeek(
            now(TIMEZONE),
            getLocale(),
            WEEK_START_MAP[session.current.user.preferences.weekStartOn]
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
                        byweekday = byweekday.filter((w) => weekday !== w);
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
