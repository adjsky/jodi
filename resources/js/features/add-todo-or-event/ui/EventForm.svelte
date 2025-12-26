<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { ArrowRight, CalendarClock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { create } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import Switch from "$/shared/ui/Switch.svelte";
    import TimeInput from "$/shared/ui/TimeInput.svelte";
    import dayjs from "dayjs";

    const searchParams = useSearchParams();
    const day = $state(dayjs(searchParams["d"]));

    let isAllDay = $state(false);
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
        startsAt: dayjs(day.format(`YYYY-MM-DD ${data.startsAt}`))
            .utc()
            .toISOString(),
        endsAt: data.endsAt
            ? dayjs(day.format(`YYYY-MM-DD ${data.endsAt}`))
                  .utc()
                  .toISOString()
            : null,
        isAllDay: Boolean(data.isAllDay)
    })}
    let:processing
>
    <div class="flex items-center justify-between">
        <h4 class="flex items-center gap-1.5 text-lg font-bold">
            <CalendarClock />
            {new Intl.DateTimeFormat(getLocale(), {
                day: "2-digit",
                year: "numeric",
                month: "short",
                weekday: "short"
            }).format(day.toDate())}
        </h4>
        <SaveOrClose variant="save" disabled={processing} />
    </div>
    <Event.Title class="mt-5" name="title" required autofocus />
    <div class="mt-3 flex items-center gap-4 text-lg">
        <Switch
            bind:checked={isAllDay}
            label={m["events.all-day"]()}
            name="isAllDay"
        />
        <div class="flex items-center gap-2">
            <TimeInput name="startsAt" required />
            <ArrowRight class="text-2xl" />
            {#if !isAllDay}
                <TimeInput name="endsAt" required />
            {:else}
                <TimeInput disabled value="00:00" />
            {/if}
        </div>
    </div>
    <Event.Description name="description" class="mt-6" />
</Form>
