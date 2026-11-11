<script lang="ts">
    import { toCalendarDate, toZoned } from "@internationalized/date";
    import { Todo } from "#entities/todo/mod.ts";
    import { SelectedDate } from "#features/choose-date/mod.ts";
    import CalendarPicker from "#features/choose-date/ui/CalendarPicker.svelte";
    import { SaveOrClose } from "#features/save-item/mod.ts";
    import { TodoTime } from "#features/schedule-todo-time/mod.ts";
    import {
        NOTIFICATION_DEFAULT_SUBHOURS,
        TIMEZONE
    } from "#shared/cfg/constants.ts";
    import { timediff } from "#shared/lib/date/timediff.ts";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        onClose: VoidFunction;
    };

    let { onClose }: Props = $props();

    let title = $state("");
    let description = $state("");
    let scheduledAt = $state(toZoned(SelectedDate.current, TIMEZONE));
    let hasTime = $state(false);

    let notifyAt = $state<ZonedDateTime | null>(null);
</script>

<Todo.Fields bind:title bind:description {scheduledAt}>
    {#snippet calendar(trigger)}
        <CalendarPicker
            mode="single"
            selected={[toCalendarDate(scheduledAt)]}
            onSelect={([d]) => {
                scheduledAt = scheduledAt.set(d);
            }}
        >
            {#snippet children(props)}
                {@render trigger(props)}
            {/snippet}
        </CalendarPicker>
    {/snippet}
    {#snippet close()}
        <SaveOrClose variant="save" />
    {/snippet}
    {#snippet category()}{/snippet}
    {#snippet time()}
        <TodoTime
            bind:hasTime
            {scheduledAt}
            onChange={(time, hasTime) => {
                if (!hasTime) {
                    notifyAt = null;
                } else {
                    if (notifyAt) {
                        notifyAt = notifyAt.add(timediff(scheduledAt, time));
                    } else {
                        notifyAt = scheduledAt.set(time).subtract({
                            hours: NOTIFICATION_DEFAULT_SUBHOURS
                        });
                    }
                }

                scheduledAt = scheduledAt.set(time);
            }}
        />
    {/snippet}
    {#snippet destroy()}{/snippet}
    {#snippet repeat()}{/snippet}
    {#snippet color()}{/snippet}
    {#snippet notify()}{/snippet}
    {#snippet more()}{/snippet}
</Todo.Fields>

<!--
<Form
    action={CreateTodo()}
    options={{
        only: ["todos"],
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        replace: true
    }}
    transform={(data) => ({
        ...data,
        hasTime,
        scheduledAt: hasTime
            ? normalizeIsoString(scheduledAt.toAbsoluteString())
            : toCalendarDate(scheduledAt).toString()
    })}
    onSuccess={() => {
        if (notifyAt) {
            Push.subscription.ahtung(m["todos.reminder-ahtung"]());
        }
        onClose();
    }}
    class="flex grow flex-col"
>
    {#snippet children({ processing })}
        <Todo.Fields {scheduledAt}>
            {#snippet category()}
                <Category
                    name="categoryId"
                    current={null}
                    deferHistoryViewFrames={DEFER_FRAMES.SHEET + 1}
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
                <Recurrence
                    day={scheduledAt}
                    name="rrule"
                    tooltip={m["todos.tooltips.repeat"]()}
                    deferHistoryViewFrames={DEFER_FRAMES.SHEET + 1}
                />
            {/snippet}
            {#snippet color()}
                <Color name="color" tooltip={m["todos.tooltips.color"]()} />
            {/snippet}
            {#snippet notify()}
                <Reminder
                    bind:notifyAt
                    startsAt={scheduledAt}
                    name="notifyAt"
                    tooltip={m["todos.tooltips.notification"]()}
                    beforeOpen={() => {
                        if (!hasTime) {
                            toaster.info(m["todos.reminder.select-time"]());
                            return false;
                        }
                    }}
                    deferHistoryViewFrames={DEFER_FRAMES.SHEET + 1}
                />
            {/snippet}
            {#snippet more()}
                <RescheduleItem
                    startsAt={toCalendarDate(scheduledAt)}
                    tooltip={m["todos.tooltips.more"]()}
                    onReschedule={(d) => {
                        if (notifyAt) {
                            notifyAt = notifyAt.set(d);
                        }
                        scheduledAt = scheduledAt.set(d);
                    }}
                />
            {/snippet}
        </Todo.Fields>
    {/snippet}
</Form> -->
