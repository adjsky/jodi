<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Bell,
        Ellipsis,
        RotateCw,
        Trash
    } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { Color } from "$/features/select-color";
    import { create } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import TimeInput from "$/shared/ui/TimeRangePicker.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { CalendarDate } from "@internationalized/date";

    type Props = {
        day: CalendarDate;
        onCalendarOpen: VoidFunction;
        onClose: VoidFunction;
    };

    const { day, onCalendarOpen, onClose }: Props = $props();
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
    <Event.Fields startsAt={day} {onCalendarOpen}>
        {#snippet close()}
            <SaveOrClose variant="save" disabled={processing} />
        {/snippet}
        {#snippet timePicker()}
            <div class="flex items-center gap-1.5">
                <TimeInput name="startsAt" required />
                <ArrowRight class="text-2xl" />
                <TimeInput name="endsAt" required />
            </div>
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
            <ToolbarAction
                disabled
                tooltip={m["events.tooltips.notification"]()}
            >
                <Bell />
            </ToolbarAction>
        {/snippet}
        {#snippet more()}
            <ToolbarAction disabled tooltip={m["events.tooltips.more"]()}>
                <Ellipsis />
            </ToolbarAction>
        {/snippet}
    </Event.Fields>
</Form>
