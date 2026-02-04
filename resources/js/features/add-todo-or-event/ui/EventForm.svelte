<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { toCalendarDate } from "@internationalized/date";
    import { Ellipsis, RotateCw, Trash } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { Color } from "$/features/select-color";
    import { Reminder } from "$/features/select-reminder";
    import { create } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { NOTIFICATION_DEFAULT_SUBHOURS } from "$/shared/cfg/constants";
    import { diff } from "$/shared/lib/date";
    import { cleanFormPayload } from "$/shared/lib/form";
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
        ...cleanFormPayload(data),
        startsAt: startsAt.toAbsoluteString(),
        endsAt: endsAt.toAbsoluteString()
    })}
    onSuccess={() => onClose()}
    class="flex grow flex-col pb-18"
    let:processing
>
    <Event.Fields
        {startsAt}
        bind:endsAt
        onStartsAtChange={(time) => {
            if (notifyAt) {
                notifyAt = notifyAt.add(diff(startsAt, time));
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
                bind:current={notifyAt}
                name="notifyAt"
                tooltip={m["todos.tooltips.notification"]()}
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
