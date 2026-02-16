<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        toCalendarDate
    } from "@internationalized/date";
    import { Ellipsis, RotateCw } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { DeleteItem } from "$/features/delete-item";
    import { YearCalendarDialog } from "$/features/filter-by-date";
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
    import Sheet from "$/shared/ui/Sheet.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { watch } from "runed";
    import { tick } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        open: boolean;
        event: App.Data.EventDto | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    let dateAnnouncerInput: HTMLInputElement | null = $state(null);

    // --------------------------- OVERRIDES -----------------------------------

    let startsAtOverride = $state<ZonedDateTime | null>(null);
    let endsAtOverride = $state<ZonedDateTime | null>(null);
    let notifyAtOverride = $state<ZonedDateTime | null>(null);

    function resetOverrides() {
        startsAtOverride = null;
        endsAtOverride = null;
        notifyAtOverride = null;
    }

    // --------------------------- EVENT DATA ----------------------------------

    let lastKnownEvent = $state(props.event);
    let event = $derived(props.event ?? (lastKnownEvent as App.Data.EventDto));

    let startsAt = $derived(
        startsAtOverride ?? parseAbsoluteToLocal(event.startsAt)
    );
    let endsAt = $derived(endsAtOverride ?? parseAbsoluteToLocal(event.endsAt));
    let notifyAt = $derived(
        notifyAtOverride ?? parseAbsoluteToLocal(event.notifyAt)
    );

    watch(
        () => [props.event],
        () => {
            if (!props.event) return;
            lastKnownEvent = props.event;
        }
    );
</script>

<Sheet
    bind:open
    defaultSnapPoint={0.6}
    snapPoints={[0.6, 0.9]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
    onCloseComplete={() => {
        resetOverrides();
    }}
>
    <Form
        {...optimistic.edit(event.id)}
        action={update(event.id)}
        options={visitOptions}
        showProgress={false}
        transform={(data) => ({
            ...cleanFormPayload(data),
            startsAt: normalizeIsoString(startsAt.toAbsoluteString()),
            endsAt: normalizeIsoString(endsAt.toAbsoluteString())
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
            value={toCalendarDate(startsAt).toString()}
        />
        <Event.Fields
            {startsAt}
            bind:endsAt={() => endsAt, (t) => (endsAtOverride = endsAt.set(t))}
            title={event.title}
            description={event.description}
            onStartsAtChange={(time) => {
                notifyAtOverride = notifyAt.add(diff(startsAt, time));
                startsAtOverride = startsAt.set(time);
            }}
        >
            {#snippet calendar(trigger)}
                <YearCalendarDialog
                    selected={toCalendarDate(startsAt)}
                    onSelect={async (d) => {
                        notifyAtOverride = notifyAt.set(d);
                        startsAtOverride = startsAt.set(d);
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
                            open = false;
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
                    name="color"
                    tooltip={m["events.tooltips.color"]()}
                    current={event.color}
                />
            {/snippet}
            {#snippet notify()}
                <Reminder
                    bind:current={notifyAt}
                    name="notifyAt"
                    tooltip={m["events.tooltips.notification"]()}
                    start={startsAt}
                />
            {/snippet}
            {#snippet more()}
                <ToolbarAction disabled tooltip={m["events.tooltips.more"]()}>
                    <Ellipsis />
                </ToolbarAction>
            {/snippet}
        </Event.Fields>
    </Form>
</Sheet>
