<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { toCalendarDate } from "@internationalized/date";
    import { Ellipsis, RotateCw, Trash } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { TodoTime } from "$/features/schedule-todo-time";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import Reminder from "$/features/select-reminder/ui/Reminder.svelte";
    import { create } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { NOTIFICATION_DEFAULT_SUBHOURS } from "$/shared/cfg/constants";
    import { diff } from "$/shared/lib/date";
    import { cleanFormPayload } from "$/shared/lib/form";
    import { toaster } from "$/shared/lib/toaster";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        day: ZonedDateTime;
        onClose: VoidFunction;
    };

    let { day: scheduledAt = $bindable(), onClose }: Props = $props();

    let hasTime = $state(false);
    let notifyAt = $state<ZonedDateTime | null>(null);
</script>

<Form
    action={create()}
    options={{
        only: ["todos", "categories"],
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        replace: true
    }}
    transform={(data) => ({
        ...cleanFormPayload(data),
        scheduledAt: scheduledAt.toAbsoluteString()
    })}
    onSuccess={() => onClose()}
    class="flex grow flex-col pb-18"
    let:processing
>
    <Todo.Fields {scheduledAt}>
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(scheduledAt)}
                onSelect={(d) => {
                    if (notifyAt) {
                        notifyAt = notifyAt.set(d);
                    }
                    scheduledAt = scheduledAt.set(d);
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
        {#snippet category()}
            <Category name="category" current={null} />
        {/snippet}
        {#snippet time()}
            <TodoTime
                {scheduledAt}
                bind:hasTime
                onChange={(time, hasTime) => {
                    if (!hasTime) {
                        notifyAt = null;
                    } else {
                        if (notifyAt) {
                            notifyAt = notifyAt.add(diff(scheduledAt, time));
                        } else {
                            notifyAt = scheduledAt.subtract({
                                hours: NOTIFICATION_DEFAULT_SUBHOURS
                            });
                        }
                    }

                    scheduledAt = scheduledAt.set(time);
                }}
            />
        {/snippet}
        {#snippet destroy()}
            <ToolbarAction
                tooltip={m["todos.tooltips.delete"]()}
                onclick={onClose}
            >
                <Trash />
            </ToolbarAction>
        {/snippet}
        {#snippet repeat()}
            <ToolbarAction disabled tooltip={m["todos.tooltips.repeat"]()}>
                <RotateCw />
            </ToolbarAction>
        {/snippet}
        {#snippet color()}
            <Color
                name="color"
                tooltip={m["todos.tooltips.color"]()}
                current={null}
            />
        {/snippet}
        {#snippet notify()}
            <Reminder
                bind:current={notifyAt}
                name="notifyAt"
                tooltip={m["todos.tooltips.notification"]()}
                start={scheduledAt}
                beforeOpen={() => {
                    if (!hasTime) {
                        toaster.info(m["todos.reminder.select-time"]());
                        return false;
                    }
                }}
            />
        {/snippet}
        {#snippet more()}
            <ToolbarAction disabled tooltip={m["todos.tooltips.more"]()}>
                <Ellipsis />
            </ToolbarAction>
        {/snippet}
    </Todo.Fields>
</Form>
