<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        toCalendarDate
    } from "@internationalized/date";
    import { RotateCw } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { DeleteItem } from "$/features/delete-item";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { Color } from "$/features/select-color";
    import { Reminder } from "$/features/select-reminder";
    import {
        destroy as _destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { diff, normalizeIsoString } from "$/shared/lib/date";
    import { announce, cleanFormPayload } from "$/shared/lib/form";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { watch } from "runed";
    import { tick, untrack } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        event: App.Data.EventDto | null;
        onClose?: VoidFunction;
    };

    const { onClose, ...props }: Props = $props();

    let dateAnnouncerInput: HTMLInputElement | null = $state(null);
    let lastKnownEvent = $state(props.event);

    const event = $derived(
        props.event ?? (lastKnownEvent as App.Data.EventDto)
    );

    const draft = $state(
        untrack(() => ({
            startsAt: parseAbsoluteToLocal(event.startsAt),
            endsAt: parseAbsoluteToLocal(event.endsAt),
            notifyAt: parseAbsoluteToLocal(event.notifyAt),
            color: event.color
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
    {...optimistic.edit(event.id)}
    action={update(event.id)}
    options={visitOptions}
    showProgress={false}
    transform={(data) => ({
        ...cleanFormPayload(data),
        startsAt: normalizeIsoString(draft.startsAt.toAbsoluteString()),
        endsAt: normalizeIsoString(draft.endsAt.toAbsoluteString())
    })}
    onSuccess={() => {
        PushSubscription.ahtung(m["events.reminder-ahtung"]());
    }}
    class="flex grow flex-col pb-18"
    let:isDirty
>
    <!-- keep to mark form dirty when selecting date in calendar -->
    <input
        bind:this={dateAnnouncerInput}
        hidden
        name="_date"
        value={toCalendarDate(draft.startsAt).toString()}
    />
    <Event.Fields
        bind:endsAt={draft.endsAt}
        startsAt={draft.startsAt}
        title={event.title}
        description={event.description}
        onStartsAtChange={(time) => {
            draft.notifyAt = draft.notifyAt.add(diff(draft.startsAt, time));
            draft.startsAt = draft.startsAt.set(time);
        }}
    >
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(draft.startsAt)}
                onSelect={async (d) => {
                    draft.notifyAt = draft.notifyAt.set(d);
                    draft.startsAt = draft.startsAt.set(d);
                    await tick();
                    announce(dateAnnouncerInput);
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
            <ToolbarAction disabled tooltip={m["events.tooltips.repeat"]()}>
                <RotateCw />
            </ToolbarAction>
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
                bind:startsAt={draft.startsAt}
                tooltip={m["events.tooltips.more"]()}
            />
        {/snippet}
    </Event.Fields>
</Form>
