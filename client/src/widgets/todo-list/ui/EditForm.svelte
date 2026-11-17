<script lang="ts">
    import { toCalendarDate } from "@internationalized/date";
    import { Todo, TodoService } from "#entities/todo/mod.ts";
    import { CalendarPicker } from "#features/choose-date/mod.ts";
    import { Checkbox } from "#features/complete-todo/mod.ts";
    import { DeleteItem } from "#features/delete-item/mod.ts";
    import { RescheduleItem } from "#features/reschedule-item/mod.ts";
    import { SaveOrClose } from "#features/save-item/mod.ts";
    import { TodoTime } from "#features/schedule-todo-time/mod.ts";
    import { Category } from "#features/select-category/mod.ts";
    import { Color } from "#features/select-color/mod.ts";
    import { Recurrence } from "#features/select-recurrence/mod.ts";
    import { Reminder } from "#features/select-reminder/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { toaster } from "#shared/ui/toaster.ts";
    import { isDeepEqual } from "remeda";
    import { untrack } from "svelte";

    import type { TodoDraft, TodoOccurrence } from "#entities/todo/mod.ts";

    type Props = {
        todo: TodoOccurrence;
        on_close?: VoidFunction;
    };

    const { todo, on_close }: Props = $props();

    const draft: TodoDraft = $state(
        untrack(() => ({
            title: todo.title,
            description: todo.description,
            category_id: todo.category_id,
            color: todo.color,
            rrule: todo.rrule,
            scheduled_at: todo.scheduled_at,
            has_time: todo.has_time,
            notify_before: todo.notify_before
        }))
    );
    const initial_draft = { ...draft };

    const is_dirty = $derived(!isDeepEqual(initial_draft, draft));
    const is_r_rule_dirty = $derived(todo.rrule !== draft.rrule);
</script>

<Todo.Fields
    bind:title={draft.title}
    bind:description={draft.description}
    scheduled_at={draft.scheduled_at}
    is_completed={todo.completed_at !== null}
>
    {#snippet calendar(trigger)}
        <CalendarPicker
            mode="single"
            selected={[toCalendarDate(draft.scheduled_at)]}
            min={todo.recurring_since}
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
            {on_close}
            title={m["todos.recurrence-action.edit-title"]()}
            variant={is_dirty ? "save" : "close"}
            scope_labels={{
                this: m["todos.recurrence-action.this"](),
                all: m["todos.recurrence-action.all"]()
            }}
            confirm={todo.rrule !== null && !is_r_rule_dirty}
            on_save={async (scope) => {
                await TodoService.update(todo.id, draft, todo.occurs_at, scope);
                on_close?.();
            }}
        />
    {/snippet}
    {#snippet category()}
        <Category bind:category_id={draft.category_id} />
    {/snippet}
    {#snippet checkbox()}
        <Checkbox
            id={todo.id}
            completed_at={todo.completed_at}
            occurs_at={todo.occurs_at}
            class="size-6 text-lg"
        />
    {/snippet}
    {#snippet time()}
        <TodoTime
            scheduled_at={draft.scheduled_at}
            has_time={draft.has_time}
            on_change={async (time, ht) => {
                draft.has_time = ht;
                draft.scheduled_at = draft.scheduled_at.set(time);
            }}
        />
    {/snippet}
    {#snippet destroy()}
        <DeleteItem
            title={{
                recurring: m["todos.recurrence-action.delete-title"](),
                general: m["todos.delete-ahtung"]()
            }}
            tooltip={m["todos.tooltips.delete"]()}
            recurring={todo.rrule !== null}
            scope_labels={{
                this: m["todos.recurrence-action.this"](),
                following: m["todos.recurrence-action.following"](),
                all: m["todos.recurrence-action.all"]()
            }}
            on_delete={async (scope) => {
                await TodoService.destroy(
                    todo.id,
                    todo.occurs_at,
                    toCalendarDate(todo.scheduled_at),
                    scope
                );
                on_close?.();
            }}
        />
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
            on_reschedule={async (d) => {
                draft.scheduled_at = draft.scheduled_at.set(d);
            }}
        />
    {/snippet}
</Todo.Fields>
