<script lang="ts">
    import { inertia, page } from "@inertiajs/svelte";
    import { DateFormatter, parseDate, today } from "@internationalized/date";
    import { Calendar as CalendarIcon } from "@lucide/svelte";
    import { Calendar, WeekCarousel } from "$/features/choose-date";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import {
        HistoryView,
        useSearchParams
    } from "$/shared/integrations/inertia";
    import { fromAction } from "svelte/attachments";

    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();

    const searchParams = useSearchParams({ showProgress: true, push: true });

    const user = $derived($page.props.auth.user);
    const selected = $derived(
        searchParams["d"] ? parseDate(searchParams["d"]) : today(TIMEZONE)
    );
    let cursor = $derived(selected);

    const view = new HistoryView("calendar", { viewTransition: true });
</script>

<header
    class="sticky top-0 z-10 flex items-center justify-between bg-cream-50 px-4 pt-2"
>
    <button class="-ms-2 p-2.5" onclick={() => view.push()}>
        <CalendarIcon class="text-3xl" />
    </button>
    <button
        class="absolute left-1/2 flex -translate-x-1/2 flex-col"
        onclick={() => {
            const date = today(TIMEZONE);

            if (date.compare(selected) == 0) {
                cursor = date;
            } else {
                void searchParams.update({ d: date.toString() });
            }
        }}
    >
        <span class="text-center text-xl font-bold">
            {new DateFormatter(getLocale(), { weekday: "long" }).format(
                selected.toDate(TIMEZONE)
            )}
        </span>
        <span class="text-center text-sm text-cream-600">
            {new DateFormatter(getLocale(), {
                year: "numeric",
                month: "long"
            }).format(selected.toDate(TIMEZONE))}
        </span>
    </button>
    {@render children()}
</header>

<WeekCarousel
    bind:selected={() => selected, (v) => (searchParams["d"] = v.toString())}
    bind:cursor
    weekStart={user.preferences.weekStartOn}
/>

{#if view.isOpen()}
    <Calendar
        attachment={(date) =>
            fromAction(inertia, () => ({
                href: `?d=${date.toString()}`,
                showProgress: true,
                replace: true,
                preserveScroll: true,
                preserveState: true,
                viewTransition: true,
                only: ["todos", "events"]
            }))}
        mode="single"
        selected={[selected]}
        weekStart={user.preferences.weekStartOn}
        onClose={() => view.back()}
    />
{/if}
