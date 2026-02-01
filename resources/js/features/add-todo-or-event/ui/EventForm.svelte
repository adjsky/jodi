<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { toZoned } from "@internationalized/date";
    import { Ellipsis, RotateCw, Trash } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { Color } from "$/features/select-color";
    import { Reminder } from "$/features/select-reminder";
    import { create } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { cleanFormPayload } from "$/shared/lib/form";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        day: CalendarDate;
        calendar: Snippet<[Snippet<[HTMLButtonAttributes]>]>;
        onClose: VoidFunction;
    };

    const { day, calendar: _calendar, onClose }: Props = $props();

    let startsAt = $derived(
        toZoned(day, TIMEZONE).set({ hour: 12, minute: 0 })
    );
    let endsAt = $derived(toZoned(day, TIMEZONE).set({ hour: 13, minute: 0 }));
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
    <Event.Fields bind:startsAt bind:endsAt>
        {#snippet calendar(trigger)}{@render _calendar(trigger)}{/snippet}
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
                name="notifyAt"
                tooltip={m["todos.tooltips.notification"]()}
                start={startsAt}
                current={null}
            />
        {/snippet}
        {#snippet more()}
            <ToolbarAction disabled tooltip={m["events.tooltips.more"]()}>
                <Ellipsis />
            </ToolbarAction>
        {/snippet}
    </Event.Fields>
</Form>
