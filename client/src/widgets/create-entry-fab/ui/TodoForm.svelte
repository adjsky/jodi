<script lang="ts">
    import { toCalendarDate, toZoned } from "@internationalized/date";
    import { Trash } from "@lucide/svelte";
    import { Todo, TodoService } from "#entities/todo/mod.ts";
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
        on_close: VoidFunction;
    };

    let { on_close }: Props = $props();

    const draft: TodoDraft = $state({
        title: "",
        description: "",
        category_id: null,
        color: null,
        rrule: null,
        scheduled_at: toZoned(SelectedDate.current, TIMEZONE),
        has_time: false,
        notify_before: "PT1H"
    });
</script>

<Todo.Fields
    bind:title={draft.title}
    bind:description={draft.description}
    scheduled_at={draft.scheduled_at}
>
    {#snippet calendar(trigger)}
        <CalendarPicker
            mode="single"
            selected={[toCalendarDate(draft.scheduled_at)]}
            on_select={([d]) => {
                draft.scheduled_at = draft.scheduled_at.set(d);
            }}
        >
            {#snippet children(props)}
                {@render trigger(props)}
            {/snippet}
        </CalendarPicker>
    {/snippet}
    {#snippet close()}
        <SaveOrClose
            variant="save"
            on_save={async () => {
                await TodoService.create(draft);
                //  Push.subscription.ahtung(m["todos.reminder-ahtung"]());
                on_close();
            }}
        />
    {/snippet}
    {#snippet category()}
        <Category bind:category_id={draft.category_id} />{/snippet}
    {#snippet time()}
        <TodoTime
            scheduled_at={draft.scheduled_at}
            has_time={draft.has_time}
            on_change={(time, ht) => {
                draft.has_time = ht;
                draft.scheduled_at = draft.scheduled_at.set(time);
            }}
        />
    {/snippet}
    {#snippet destroy()}
        <ToolbarAction tooltip={m["todos.tooltips.delete"]()} onclick={on_close}>
            <Trash />
        </ToolbarAction>
    {/snippet}
    {#snippet repeat()}
        <Recurrence
            bind:rrule={draft.rrule}
            day={draft.scheduled_at}
            tooltip={m["todos.tooltips.repeat"]()}
        />
    {/snippet}
    {#snippet color()}
        <Color bind:color={draft.color} tooltip={m["todos.tooltips.color"]()} />
    {/snippet}
    {#snippet notify()}
        <Reminder
            bind:notify_before={draft.notify_before}
            tooltip={m["todos.tooltips.notification"]()}
            disabled={!draft.has_time}
            on_disabled_attempt={() => {
                toaster.info(m["todos.reminder.select-time"]());
            }}
        />
    {/snippet}
    {#snippet more()}
        <RescheduleItem
            starts_at={toCalendarDate(draft.scheduled_at)}
            tooltip={m["todos.tooltips.more"]()}
            on_reschedule={(d) => {
                draft.scheduled_at = draft.scheduled_at.set(d);
            }}
        />
    {/snippet}
</Todo.Fields>
