<script lang="ts">
    import { toCalendarDate } from "@internationalized/date";
    import { Todo } from "#entities/todo/mod.ts";
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
        onClose?: VoidFunction;
    };

    const { todo, onClose }: Props = $props();

    const draft: TodoDraft = $state(
        untrack(() => ({
            title: todo.title,
            description: todo.description,
            categoryId: todo.categoryId,
            color: todo.color,
            rrule: todo.rrule,
            scheduledAt: todo.scheduledAt,
            hasTime: todo.hasTime,
            notifyBefore: todo.notifyBefore
        }))
    );
    const initialDraft = { ...draft };

    const isDirty = $derived(!isDeepEqual(initialDraft, draft));
    const isRRuleDirty = $derived(todo.rrule !== draft.rrule);
</script>

<Todo.Fields
    bind:title={draft.title}
    bind:description={draft.description}
    scheduledAt={draft.scheduledAt}
    isCompleted={todo.completedAt !== null}
>
    {#snippet calendar(trigger)}
        <CalendarPicker
            mode="single"
            selected={[toCalendarDate(draft.scheduledAt)]}
            min={todo.recurringSince}
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
        <SaveOrClose
            {onClose}
            title={m["todos.recurrence-action.edit-title"]()}
            variant={isDirty ? "save" : "close"}
            scopeLabels={{
                this: m["todos.recurrence-action.this"](),
                all: m["todos.recurrence-action.all"]()
            }}
            confirm={todo.rrule !== null && !isRRuleDirty}
            onSave={async (scope) => {}}
        />
    {/snippet}
    {#snippet category()}
        <Category bind:categoryId={draft.categoryId} />
    {/snippet}
    {#snippet checkbox()}
        <Checkbox
            id={todo.id}
            completedAt={todo.completedAt}
            occursAt={todo.occursAt}
            class="size-6 text-lg"
        />
    {/snippet}
    {#snippet time()}
        <TodoTime
            scheduledAt={draft.scheduledAt}
            hasTime={draft.hasTime}
            onChange={async (time, ht) => {
                draft.hasTime = ht;
                draft.scheduledAt = draft.scheduledAt.set(time);
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
            scopeLabels={{
                this: m["todos.recurrence-action.this"](),
                following: m["todos.recurrence-action.following"](),
                all: m["todos.recurrence-action.all"]()
            }}
            onDelete={(scope) => {
                console.log(scope);
            }}
        />
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
            onReschedule={async (d) => {
                draft.scheduledAt = draft.scheduledAt.set(d);
            }}
        />
    {/snippet}
</Todo.Fields>
