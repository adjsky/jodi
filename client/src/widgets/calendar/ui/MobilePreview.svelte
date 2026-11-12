<script lang="ts">
    import { DateFormatter, today } from "@internationalized/date";
    import { Calendar as CalendarIcon } from "@lucide/svelte";
    import { SessionCache } from "#entities/user/mod.ts";
    import {
        Calendar,
        SelectedDate,
        WeekCarousel
    } from "#features/choose-date/mod.ts";
    import { getLocale } from "#paraglide/runtime.js";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { TIMEZONE } from "#shared/cfg/constants.ts";

    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();

    const session = SessionCache.retain();
    const user = $derived(session.current.user);

    let cursor = $derived(SelectedDate.current);
</script>

<header class="flex items-center justify-between bg-cream-50 px-4 pt-2">
    <button class="-ms-2 p-2.5" onclick={() => Overlay.open("calendar")}>
        <CalendarIcon class="text-3xl" />
    </button>
    <button
        class="absolute left-1/2 flex -translate-x-1/2 flex-col"
        onclick={() => SelectedDate.update(today(TIMEZONE))}
    >
        <span class="text-center text-xl font-bold">
            {new DateFormatter(getLocale(), { weekday: "long" }).format(
                SelectedDate.current.toDate(TIMEZONE)
            )}
        </span>
        <span class="text-center text-sm text-cream-600">
            {new DateFormatter(getLocale(), {
                year: "numeric",
                month: "long"
            }).format(SelectedDate.current.toDate(TIMEZONE))}
        </span>
    </button>
    {@render children()}
</header>

<WeekCarousel
    bind:selected={
        () => SelectedDate.current, (date) => SelectedDate.update(date)
    }
    bind:cursor
    weekStart={user.preferences.weekStartOn}
/>

<Calendar
    bind:open={() => Overlay.isOpen("calendar"), () => Overlay.close()}
    mode="single"
    selected={[SelectedDate.current]}
    weekStart={user.preferences.weekStartOn}
    onSelect={async ([date]) => {
        void Overlay.close();
        SelectedDate.update(date);
    }}
/>
