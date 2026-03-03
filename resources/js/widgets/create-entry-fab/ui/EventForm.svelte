<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { toCalendarDate } from "@internationalized/date";
    import { RotateCw, Trash } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { Color } from "$/features/select-color";
    import { Reminder } from "$/features/select-reminder";
    import { create } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { NOTIFICATION_DEFAULT_SUBHOURS } from "$/shared/cfg/constants";
    import { timediff } from "$/shared/lib/date";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { toaster } from "$/shared/lib/toaster";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        day: ZonedDateTime;
        onClose: VoidFunction;
    };

    let { day: startsAt = $bindable(), onClose }: Props = $props();

    let endsAt = $state(startsAt.add({ hours: 1 }));
    let notifyAt = $state(
        startsAt.subtract({
            hours: NOTIFICATION_DEFAULT_SUBHOURS
        })
    );
</script>

<Form
    action={create()}
    options={{
        only: ["events"],
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        replace: true
    }}
    transform={(data) => ({
        ...data,
        startsAt: startsAt.toAbsoluteString(),
        endsAt: endsAt.toAbsoluteString()
    })}
    onBefore={() => {
        if (startsAt.compare(endsAt) >= 0) {
            toaster.error(m["common.invalid-time-range"]());
            return false;
        }
    }}
    onSuccess={() => {
        PushSubscription.ahtung(m["events.reminder-ahtung"]());
        onClose();
    }}
    class="flex grow flex-col pb-18"
    let:processing
>
    <Event.Fields
        {startsAt}
        bind:endsAt
        onStartsAtChange={(time) => {
            if (notifyAt) {
                notifyAt = notifyAt.add(timediff(startsAt, time));
            } else {
                notifyAt = startsAt.subtract({
                    hours: NOTIFICATION_DEFAULT_SUBHOURS
                });
            }
            startsAt = startsAt.set(time);
        }}
    >
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(startsAt)}
                onSelect={(d) => {
                    notifyAt = notifyAt.set(d);
                    startsAt = startsAt.set(d);
                    endsAt = endsAt.set(d);
                }}
            >
                {#snippet children(props)}
                    {@render trigger(props())}
                {/snippet}
            </YearCalendarDialog>
        {/snippet}
        {#snippet close()}
            <SaveOrClose variant="save" disabled={processing} />
        {/snippet}
        {#snippet destroy()}
            <ToolbarAction
                tooltip={m["events.tooltips.delete"]()}
                onclick={onClose}
            >
                <Trash />
            </ToolbarAction>
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
                current={null}
            />
        {/snippet}
        {#snippet notify()}
            <Reminder
                {startsAt}
                bind:notifyAt
                name="notifyAt"
                tooltip={m["todos.tooltips.notification"]()}
            />
        {/snippet}
        {#snippet more()}
            <RescheduleItem
                startsAt={toCalendarDate(startsAt)}
                tooltip={m["events.tooltips.more"]()}
                onReschedule={(d) => {
                    notifyAt = notifyAt.set(d);
                    startsAt = startsAt.set(d);
                    endsAt = endsAt.set(d);
                }}
            />
        {/snippet}
    </Event.Fields>
</Form>
