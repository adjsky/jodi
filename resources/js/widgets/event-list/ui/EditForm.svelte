<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        parseDate,
        toCalendarDate
    } from "@internationalized/date";
    import { Event } from "$/entities/event";
    import { DeleteItem } from "$/features/delete-item";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { Color } from "$/features/select-color";
    import { Recurrence } from "$/features/select-recurrence";
    import { Reminder } from "$/features/select-reminder";
    import DestroyEvent from "$/generated/actions/App/Domain/Event/Actions/DestroyEvent";
    import UpdateEvent from "$/generated/actions/App/Domain/Event/Actions/UpdateEvent";
    import { m } from "$/paraglide/messages";
    import { normalizeIsoString, timediff } from "$/shared/lib/date";
    import { announce } from "$/shared/lib/form";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import { watch } from "runed";
    import { tick, untrack } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    import type { EventData } from "$/entities/event/model/types";
    import type { Scope } from "$/shared/lib/types";

    type Props = {
        event: EventData | null;
        onClose?: VoidFunction;
    };

    const { onClose, ...props }: Props = $props();

    let startsAtAnnouncerInput: HTMLInputElement | null = $state(null);
    let endsAtAnnouncerInput: HTMLInputElement | null = $state(null);
    let lastKnownEvent = $state(untrack(() => props.event));
    let scope: Scope = $state("this");

    const event = $derived(props.event ?? (lastKnownEvent as EventData));

    const draft = $state(
        untrack(() => ({
            startsAt: parseAbsoluteToLocal(event.startsAt),
            endsAt: parseAbsoluteToLocal(event.endsAt),
            notifyAt: parseAbsoluteToLocal(event.notifyAt),
            color: event.color,
            rrule: event.rrule
        }))
    );

    const isRRuleDirty = $derived(event.rrule != draft.rrule);

    watch(
        () => [props.event],
        () => {
            if (!props.event) return;
            lastKnownEvent = props.event;
        }
    );
</script>

<Form
    {...optimistic.edit(event, draft)}
    action={UpdateEvent(event.id, {
        query: { scope: isRRuleDirty ? "all" : scope }
    })}
    options={visitOptions}
    transform={(data) => ({
        ...data,
        occursAt: event.occursAt
    })}
    showProgress={false}
    class="flex grow flex-col pb-18"
    let:isDirty
    let:submit
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
                min={event.recurringSince
                    ? parseDate(event.recurringSince)
                    : null}
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
                {onClose}
                title={m["events.recurrence-action.edit-title"]()}
                variant={isDirty ? "save" : "close"}
                scopeLabels={{
                    this: m["events.recurrence-action.this"](),
                    all: m["events.recurrence-action.all"]()
                }}
                confirm={event.rrule != null && !isRRuleDirty}
                onConfirm={async (s) => {
                    scope = s;
                    await tick();
                    submit();
                }}
            />
        {/snippet}
        {#snippet destroy()}
            <DeleteItem
                {...visitOptions}
                {...optimistic.delete(event)}
                href={DestroyEvent(event.id)}
                title={{
                    recurring: m["events.recurrence-action.delete-title"](),
                    general: m["events.delete-ahtung"]()
                }}
                tooltip={m["events.tooltips.delete"]()}
                recurring={event.rrule != null}
                occursAt={event.occursAt}
                scopeLabels={{
                    this: m["events.recurrence-action.this"](),
                    all: m["events.recurrence-action.all"]()
                }}
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
