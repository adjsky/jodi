<script lang="ts">
    import { Form, router } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        toCalendarDate
    } from "@internationalized/date";
    import { Event } from "$/entities/event";
    import { DeleteItem } from "$/features/delete-item";
    import { daySummary, YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { Color } from "$/features/select-color";
    import { Recurrence } from "$/features/select-recurrence";
    import { Reminder } from "$/features/select-reminder";
    import {
        destroy as _destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { normalizeIsoString, timediff } from "$/shared/lib/date";
    import { announce } from "$/shared/lib/form";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import { watch } from "runed";
    import { tick, untrack } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        event: App.Data.EventDto | null;
        onClose?: VoidFunction;
    };

    const { onClose, ...props }: Props = $props();

    let startsAtAnnouncerInput: HTMLInputElement | null = $state(null);
    let endsAtAnnouncerInput: HTMLInputElement | null = $state(null);
    let lastKnownEvent = $state(untrack(() => props.event));

    const event = $derived(
        props.event ?? (lastKnownEvent as App.Data.EventDto)
    );

    const draft = $state(
        untrack(() => ({
            startsAt: parseAbsoluteToLocal(event.startsAt),
            endsAt: parseAbsoluteToLocal(event.endsAt),
            notifyAt: parseAbsoluteToLocal(event.notifyAt),
            color: event.color,
            rrule: event.rrule
        }))
    );

    watch(
        () => [props.event],
        () => {
            if (!props.event) return;
            lastKnownEvent = props.event;
        }
    );
</script>

<Form
    {...optimistic.edit(event.id, () => draft)}
    action={update(event.id)}
    options={visitOptions}
    showProgress={false}
    onSuccess={() => {
        PushSubscription.ahtung(m["events.reminder-ahtung"]());
        router.flushByCacheTags("week-carousel");
        daySummary.flush();
    }}
    class="flex grow flex-col pb-18"
    let:isDirty
>
    <Event.Fields
        bind:startsAt={
            () => draft.startsAt,
            (d) => {
                draft.notifyAt = draft.notifyAt.add(
                    timediff(draft.startsAt, d)
                );
                draft.startsAt = d;
            }
        }
        bind:endsAt={draft.endsAt}
        title={event.title}
        description={event.description}
        onStartsAtChange={async () => {
            await tick();
            announce(startsAtAnnouncerInput);
        }}
        onEndsAtChange={async () => {
            await tick();
            announce(endsAtAnnouncerInput);
        }}
    >
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(draft.startsAt)}
                onSelect={async (d) => {
                    draft.notifyAt = draft.notifyAt.set(d);
                    draft.startsAt = draft.startsAt.set(d);
                    draft.endsAt = draft.endsAt.set(d);
                    await tick();
                    announce(startsAtAnnouncerInput);
                }}
            >
                {#snippet children(props)}
                    {@render trigger(props())}
                {/snippet}
            </YearCalendarDialog>
        {/snippet}
        {#snippet close()}
            <SaveOrClose
                variant={isDirty ? "save" : "close"}
                onclick={() => {
                    if (!isDirty) {
                        onClose?.();
                    }
                }}
            />
        {/snippet}
        {#snippet destroy()}
            <DeleteItem
                {...visitOptions}
                {...optimistic.delete(event.id)}
                href={_destroy(event.id)}
                title={m["events.delete-ahtung"]()}
                tooltip={m["events.tooltips.delete"]()}
            />
        {/snippet}
        {#snippet repeat()}
            <Recurrence
                bind:rrule={draft.rrule}
                day={draft.startsAt}
                name="rrule"
                tooltip={m["events.tooltips.repeat"]()}
            />
        {/snippet}
        {#snippet color()}
            <Color
                bind:current={draft.color}
                name="color"
                tooltip={m["events.tooltips.color"]()}
            />
        {/snippet}
        {#snippet notify()}
            <Reminder
                bind:notifyAt={draft.notifyAt}
                startsAt={draft.startsAt}
                name="notifyAt"
                tooltip={m["events.tooltips.notification"]()}
            />
        {/snippet}
        {#snippet more()}
            <RescheduleItem
                startsAt={toCalendarDate(draft.startsAt)}
                tooltip={m["events.tooltips.more"]()}
                onReschedule={async (d) => {
                    draft.notifyAt = draft.notifyAt.set(d);
                    draft.startsAt = draft.startsAt.set(d);
                    draft.endsAt = draft.endsAt.set(d);
                    await tick();
                    announce(startsAtAnnouncerInput);
                }}
            />
        {/snippet}
    </Event.Fields>

    <input
        bind:this={startsAtAnnouncerInput}
        hidden
        name="startsAt"
        value={normalizeIsoString(draft.startsAt.toAbsoluteString())}
    />
    <input
        bind:this={endsAtAnnouncerInput}
        hidden
        name="endsAt"
        value={normalizeIsoString(draft.endsAt.toAbsoluteString())}
    />
</Form>
