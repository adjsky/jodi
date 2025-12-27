<script lang="ts">
    import { Form, page } from "@inertiajs/svelte";
    import {
        DateFormatter,
        parseDate,
        toCalendarDate,
        today
    } from "@internationalized/date";
    import { ArrowRight, CalendarClock, Clock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { YearCalendar } from "$/features/filter-by-date";
    import { create } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import TimeInput from "$/shared/ui/TimeInput.svelte";

    import { view } from "../model/view";

    const searchParams = useSearchParams();

    let day = $derived(
        searchParams["d"] ? parseDate(searchParams["d"]) : today(TIMEZONE)
    );
</script>

<Form
    action={create()}
    options={{
        only: ["events"],
        preserveState: true,
        preserveScroll: true,
        replace: true
    }}
    transform={(data) => ({
        ...data,
        startsAt: new Date(`${day}T${data.startsAt}`).toISOString(),
        endsAt: data.endsAt
            ? new Date(`${day}T${data.endsAt}`).toISOString()
            : null
    })}
    let:processing
>
    <div class="flex items-center justify-between">
        <button
            onclick={() =>
                view.updateMeta(
                    { entity: "event", overlay: "calendar" },
                    { push: true, viewTransition: true }
                )}
            type="button"
        >
            <h4 class="flex items-center gap-1.5 text-lg font-bold">
                <CalendarClock />
                {new DateFormatter(getLocale(), {
                    day: "2-digit",
                    year: "numeric",
                    month: "short",
                    weekday: "short"
                }).format(day.toDate(TIMEZONE))}
            </h4>
        </button>
        <SaveOrClose variant="save" disabled={processing} />
    </div>
    <div class="mt-3 flex items-center gap-4 text-lg">
        <div class="flex items-center gap-1">
            <Clock class="text-2xl" />
            <span class="font-bold">{m["events.time"]()}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <TimeInput name="startsAt" required />
            <ArrowRight class="text-2xl" />
            <TimeInput name="endsAt" required />
        </div>
    </div>
    <Event.Title class="mt-5" name="title" required autofocus />
    <Event.Description name="description" class="mt-6" />
</Form>

{#if view.meta?.overlay == "calendar"}
    <YearCalendar
        selected={day}
        start={$page.props.auth.user.preferences.weekStartOn}
        onClose={() => view.back()}
        onSelect={async (date) => {
            view.back();
            day = toCalendarDate(date);
        }}
    />
{/if}
