<script lang="ts">
    import { Link, page } from "@inertiajs/svelte";
    import { DateFormatter, parseDate, today } from "@internationalized/date";
    import { Calendar } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { WeekCarousel, YearCalendar } from "$/features/filter-by-date";
    import { me } from "$/generated/routes";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";

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
    <button class="p-2.5" onclick={() => view.open()}>
        <Calendar class="text-3xl" />
    </button>
    <div class="absolute left-1/2 -translate-x-1/2">
        <h1 class="text-center text-xl font-bold">
            {new DateFormatter(getLocale(), { weekday: "long" }).format(
                cursor.toDate(TIMEZONE)
            )}
        </h1>
        <h2 class="text-center text-sm text-cream-600">
            {new DateFormatter(getLocale(), {
                year: "numeric",
                month: "long"
            }).format(cursor.toDate(TIMEZONE))}
        </h2>
    </div>
    <Link href={me()} viewTransition>
        <User.Avatar name={user.name} />
    </Link>
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
