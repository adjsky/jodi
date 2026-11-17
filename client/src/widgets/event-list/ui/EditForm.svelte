<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { parseAbsoluteToLocal, parseDate, toCalendarDate } from "@internationalized/date";
    import { Event } from "$/entities/event";
    import { CalendarPicker } from "$/features/choose-date";
    import { DeleteItem } from "$/features/delete-item";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { SaveOrClose } from "$/features/save-item";
    import { Color } from "$/features/select-color";
    import { Recurrence } from "$/features/select-recurrence";
    import { Reminder } from "$/features/select-reminder";
    import DestroyEvent from "$/generated/actions/App/Domain/Event/Actions/DestroyEvent";
    import UpdateEvent from "$/generated/actions/App/Domain/Event/Actions/UpdateEvent";
    import { m } from "$/paraglide/messages";
    import { DEFER_FRAMES } from "$/shared/cfg/constants";
    import { normalizeIsoString } from "$/shared/lib/date/normalize-iso-string";
    import { timediff } from "$/shared/lib/date/timediff";
    import { dispatchInput } from "$/shared/lib/dom/dispatch-input";
    import { tick, untrack } from "svelte";

    import { destroy as optimistic_destroy } from "../api/destroy";
    import { edit } from "../api/edit";
    import { visit_options } from "../cfg/inertia";

    import type { EventData } from "$/entities/event";
    import type { RecurrenceScope } from "$/entities/recurrence";

    type Props = {
        event: EventData;
        on_close?: VoidFunction;
    };

    const { event, on_close }: Props = $props();

    let starts_at_announcer_input: HTMLInputElement | null = $state(null);
    let ends_at_announcer_input: HTMLInputElement | null = $state(null);
    let scope: RecurrenceScope = $state("this");

    const draft = $state(
        untrack(() => ({
            starts_at: parseAbsoluteToLocal(event.startsAt),
            ends_at: parseAbsoluteToLocal(event.endsAt),
            notify_at: parseAbsoluteToLocal(event.notifyAt),
            color: event.color,
            rrule: event.rrule
        }))
    );

    const is_r_rule_dirty = $derived(event.rrule !== draft.rrule);
</script>

<Form
    {...edit(event, draft)}
    action={UpdateEvent(event.id, {
        query: { scope: is_r_rule_dirty ? "all" : scope }
    })}
    options={visit_options}
    transform={(data) => ({
        ...data,
        occursAt: event.occursAt
    })}
    showProgress={false}
    class="flex min-h-0 grow flex-col"
>
    {#snippet children({ isDirty: is_dirty, submit })}
        <Event.Fields
            bind:starts_at={
                () => draft.starts_at,
                (d) => {
                    draft.notify_at = draft.notify_at.add(timediff(draft.starts_at, d));
                    draft.starts_at = d;
                }
            }
            bind:ends_at={draft.ends_at}
            title={event.title}
            description={event.description}
            on_starts_at_change={async () => {
                await tick();
                dispatchInput(starts_at_announcer_input);
            }}
            on_ends_at_change={async () => {
                await tick();
                dispatchInput(ends_at_announcer_input);
            }}
        >
            {#snippet calendar(trigger)}
                <CalendarPicker
                    mode="range"
                    selected={[toCalendarDate(draft.starts_at), toCalendarDate(draft.ends_at)]}
                    min={event.recurringSince ? parseDate(event.recurringSince) : null}
                    defer_history_view_frames={DEFER_FRAMES.SHEET + 1}
                    on_select={async (date) => {
                        draft.notify_at = draft.notify_at.set(date[0]);
                        draft.starts_at = draft.starts_at.set(date[0]);
                        draft.ends_at = draft.ends_at.set(date[1]);
                        await tick();
                        dispatchInput(starts_at_announcer_input);
                    }}
                >
                    {#snippet children(props)}
                        {@render trigger(props)}
                    {/snippet}
                </CalendarPicker>
            {/snippet}
            {#snippet close()}
                <SaveOrClose
                    {on_close}
                    title={m["events.recurrence-action.edit-title"]()}
                    variant={is_dirty ? "save" : "close"}
                    scope_labels={{
                        this: m["events.recurrence-action.this"](),
                        all: m["events.recurrence-action.all"]()
                    }}
                    confirm={event.rrule !== null && !is_r_rule_dirty}
                    on_confirm={async (s) => {
                        scope = s;
                        await tick();
                        submit();
                    }}
                />
            {/snippet}
            {#snippet destroy()}
                <DeleteItem
                    {...optimistic_destroy(event)}
                    {...visit_options}
                    href={DestroyEvent(event.id)}
                    title={{
                        recurring: m["events.recurrence-action.delete-title"](),
                        general: m["events.delete-ahtung"]()
                    }}
                    tooltip={m["events.tooltips.delete"]()}
                    recurring={event.rrule !== null}
                    occurs_at={event.occursAt}
                    date={event.startsAt}
                    scope_labels={{
                        this: m["events.recurrence-action.this"](),
                        following: m["events.recurrence-action.following"](),
                        all: m["events.recurrence-action.all"]()
                    }}
                    defer_history_view_frames={DEFER_FRAMES.SHEET + 1}
                />
            {/snippet}
            {#snippet repeat()}
                <Recurrence
                    bind:rrule={draft.rrule}
                    day={draft.starts_at}
                    name="rrule"
                    tooltip={m["events.tooltips.repeat"]()}
                    defer_history_view_frames={DEFER_FRAMES.SHEET + 1}
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
                    bind:notify_at={draft.notify_at}
                    starts_at={draft.starts_at}
                    name="notifyAt"
                    tooltip={m["events.tooltips.notification"]()}
                    defer_history_view_frames={DEFER_FRAMES.SHEET + 1}
                />
            {/snippet}
            {#snippet more()}
                <RescheduleItem
                    starts_at={toCalendarDate(draft.starts_at)}
                    tooltip={m["events.tooltips.more"]()}
                    on_reschedule={async (d) => {
                        draft.notify_at = draft.notify_at.set(d);
                        draft.starts_at = draft.starts_at.set(d);
                        draft.ends_at = draft.ends_at.set(d);
                        await tick();
                        dispatchInput(starts_at_announcer_input);
                    }}
                />
            {/snippet}
        </Event.Fields>

        <input
            bind:this={starts_at_announcer_input}
            hidden
            name="startsAt"
            value={normalizeIsoString(draft.starts_at.toAbsoluteString())}
        />
        <input
            bind:this={ends_at_announcer_input}
            hidden
            name="endsAt"
            value={normalizeIsoString(draft.ends_at.toAbsoluteString())}
        />
    {/snippet}
</Form>
