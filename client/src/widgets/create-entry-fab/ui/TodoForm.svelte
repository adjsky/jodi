<script lang="ts">
    import { toCalendarDate, toZoned } from "@internationalized/date";
    import { Trash } from "@lucide/svelte";
    import { Todo } from "#entities/todo/mod.ts";
    import { CalendarPicker, SelectedDate } from "#features/choose-date/mod.ts";
    import { RescheduleItem } from "#features/reschedule-item/mod.ts";
    import { SaveOrClose } from "#features/save-item/mod.ts";
    import { TodoTime } from "#features/schedule-todo-time/mod.ts";
    import { Category } from "#features/select-category/mod.ts";
    import { Color } from "#features/select-color/mod.ts";
    import { Recurrence } from "#features/select-recurrence/mod.ts";
    import { Reminder } from "#features/select-reminder/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { TIMEZONE } from "#shared/cfg/constants.ts";
    import { toaster } from "#shared/ui/toaster.ts";
    import ToolbarAction from "#shared/ui/ToolbarAction.svelte";

    import type { TodoDraft } from "#entities/todo/mod.ts";

    type Props = {
        onClose: VoidFunction;
    };

    let { onClose }: Props = $props();

    const draft: TodoDraft = $state({
        title: "",
        description: "",
        categoryId: null,
        color: null,
        rrule: null,
        scheduledAt: toZoned(SelectedDate.current, TIMEZONE),
        hasTime: false,
        notifyBefore: "PT1H"
    });
</script>

<Todo.Fields
    bind:title={draft.title}
    bind:description={draft.description}
    scheduledAt={draft.scheduledAt}
>
    {#snippet calendar(trigger)}
        <CalendarPicker
            mode="single"
            selected={[toCalendarDate(draft.scheduledAt)]}
            onSelect={([d]) => {
                draft.scheduledAt = draft.scheduledAt.set(d);
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
    {#snippet category()}
        <Category bind:categoryId={draft.categoryId} />{/snippet}
    {#snippet time()}
        <TodoTime
            scheduledAt={draft.scheduledAt}
            hasTime={draft.hasTime}
            onChange={(time, ht) => {
                draft.hasTime = ht;
                draft.scheduledAt = draft.scheduledAt.set(time);
            }}
        />
    {/snippet}
    {#snippet destroy()}
        <ToolbarAction tooltip={m["todos.tooltips.delete"]()} onclick={onClose}>
            <Trash />
        </ToolbarAction>
    {/snippet}
    {#snippet repeat()}
        <Recurrence
            bind:rrule={draft.rrule}
            day={draft.scheduledAt}
            tooltip={m["todos.tooltips.repeat"]()}
        />
    {/snippet}
    {#snippet color()}
        <Color bind:color={draft.color} tooltip={m["todos.tooltips.color"]()} />
    {/snippet}
    {#snippet notify()}
        <Reminder
            bind:notifyBefore={draft.notifyBefore}
            tooltip={m["todos.tooltips.notification"]()}
            disabled={!draft.hasTime}
            onDisabledAttempt={() => {
                toaster.info(m["todos.reminder.select-time"]());
            }}
        />
    {/snippet}
    {#snippet more()}
        <RescheduleItem
            startsAt={toCalendarDate(draft.scheduledAt)}
            tooltip={m["todos.tooltips.more"]()}
            onReschedule={(d) => {
                draft.scheduledAt = draft.scheduledAt.set(d);
            }}
        />
    {/snippet}
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

        </Todo.Fields>
    {/snippet}
</Form> -->
