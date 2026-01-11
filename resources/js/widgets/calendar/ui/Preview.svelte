<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { DateFormatter, parseDate, today } from "@internationalized/date";
    import { Calendar } from "@lucide/svelte";
    import { WeekCarousel, YearCalendar } from "$/features/filter-by-date";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();

    const searchParams = useSearchParams({ showProgress: true });

    const user = $derived($page.props.auth.user);
    const selected = $derived(
        searchParams["d"] ? parseDate(searchParams["d"]) : today(TIMEZONE)
    );
    let cursor = $derived(selected);

    const view = new HistoryView("calendar", { viewTransition: true });
</script>

<header
    class="sticky top-0 z-10 flex items-center justify-between bg-cream-50 py-2 pr-6 pl-3"
>
    <button class="p-2.5" onclick={() => view.push()}>
        <Calendar class="text-3xl" />
    </button>
    <button
        class="absolute left-1/2 flex -translate-x-1/2 flex-col"
        onclick={() => searchParams.update({ d: today(TIMEZONE).toString() })}
    >
        <span class="text-center text-xl font-bold">
            {new DateFormatter(getLocale(), { weekday: "long" }).format(
                cursor.toDate(TIMEZONE)
            )}
        </span>
        <span class="text-center text-sm text-cream-600">
            {new DateFormatter(getLocale(), {
                year: "numeric",
                month: "long"
            }).format(cursor.toDate(TIMEZONE))}
        </span>
    </button>
    {@render children()}
</header>

<WeekCarousel
    bind:selected={() => selected, (v) => (searchParams["d"] = v.toString())}
    bind:cursor
    start={user.preferences.weekStartOn}
/>

{#if view.isOpen()}
    <YearCalendar
        {selected}
        start={user.preferences.weekStartOn}
        onClose={() => view.back()}
    />
{/if}
