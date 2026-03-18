<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        parseDate,
        toCalendarDate
    } from "@internationalized/date";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { DeleteItem } from "$/features/delete-item";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { TodoTime } from "$/features/schedule-todo-time";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import { Recurrence } from "$/features/select-recurrence";
    import { Reminder } from "$/features/select-reminder";
    import {
        destroy as _destroy,
        complete,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { NOTIFICATION_DEFAULT_SUBHOURS } from "$/shared/cfg/constants";
    import { normalizeIsoString, timediff } from "$/shared/lib/date";
    import { announce } from "$/shared/lib/form";
    import { toaster } from "$/shared/lib/toaster";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import { watch } from "runed";
    import { tick, untrack } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    import type { Scope } from "$/shared/lib/types";

    type Props = {
        todo: App.Data.TodoDto | null;
        onClose?: VoidFunction;
    };

    const { onClose, ...props }: Props = $props();

    let dateAnnouncerInput: HTMLInputElement | null = $state(null);
    let lastKnownTodo = $state(untrack(() => props.todo));

    let todo = $derived(props.todo ?? (lastKnownTodo as App.Data.TodoDto));

    const draft = $state(
        untrack(() => ({
            scheduledAt: parseAbsoluteToLocal(todo.scheduledAt),
            hasTime: todo.hasTime,
            notifyAt: todo.notifyAt
                ? parseAbsoluteToLocal(todo.notifyAt)
                : null,
            color: todo.color,
            rrule: todo.rrule
        }))
    );
    const occursAt = $derived(todo.scheduledAt.split("T")[0]);

    let scope: Scope = $state("this");

    watch(
        () => [props.todo],
        () => {
            if (!props.todo) return;
            lastKnownTodo = props.todo;
        }
    );
</script>

<Form
    {...optimistic.edit(todo.id, draft.notifyAt != null)}
    action={update(todo.id)}
    options={visitOptions}
    transform={(data) => ({
        ...data,
        hasTime: draft.hasTime,
        occursAt,
        scope
    })}
    showProgress={false}
    class="flex grow flex-col pb-18"
    let:isDirty
    let:submit
>
    <Todo.Fields
        scheduledAt={draft.scheduledAt}
        title={todo.title}
        description={todo.description}
        isCompleted={todo.completedAt != null}
    >
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(draft.scheduledAt)}
                min={todo.recurringSince
                    ? parseDate(todo.recurringSince)
                    : null}
                onSelect={async (d) => {
                    if (draft.notifyAt) {
                        draft.notifyAt = draft.notifyAt.set(d);
                    }
                    draft.scheduledAt = draft.scheduledAt.set(d);
                    await tick();
                    announce(dateAnnouncerInput);
                }}
            >
                {#snippet children(props)}
                    {@render trigger(props())}
                {/snippet}
            </YearCalendarDialog>
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
                confirm={todo.rrule != null && todo.rrule === draft.rrule}
                onConfirm={async (s) => {
                    scope = s;
                    await tick();
                    submit();
                }}
            />
        {/snippet}
        {#snippet category()}
            <Category name="category" current={todo.category} />
        {/snippet}
        {#snippet checkbox()}
            <Checkbox
                {...visitOptions}
                {...optimistic.complete(todo.id)}
                {occursAt}
                href={complete(todo.id)}
                completedAt={todo.completedAt}
                class="size-6 text-lg"
            />
        {/snippet}
        {#snippet time()}
            <TodoTime
                bind:hasTime={draft.hasTime}
                scheduledAt={draft.scheduledAt}
                onChange={async (time, hasTime) => {
                    if (!hasTime) {
                        draft.notifyAt = null;
                    } else if (draft.notifyAt) {
                        draft.notifyAt = draft.notifyAt.add(
                            timediff(draft.scheduledAt, time)
                        );
                    } else {
                        draft.notifyAt = draft.scheduledAt.set(time).subtract({
                            hours: NOTIFICATION_DEFAULT_SUBHOURS
                        });
                    }

                    draft.scheduledAt = draft.scheduledAt.set(time);

                    await tick();
                    announce(dateAnnouncerInput);
                }}
            />
        {/snippet}
        {#snippet destroy()}
            <DeleteItem
                {...visitOptions}
                {...optimistic.delete(todo.id)}
                href={_destroy(todo.id)}
                title={{
                    recurring: m["todos.recurrence-action.delete-title"](),
                    general: m["todos.delete-ahtung"]()
                }}
                tooltip={m["todos.tooltips.delete"]()}
                recurring={todo.rrule != null}
                date={todo.scheduledAt}
                scopeLabels={{
                    this: m["todos.recurrence-action.this"](),
                    all: m["todos.recurrence-action.all"]()
                }}
            />
        {/snippet}
        {#snippet repeat()}
            <Recurrence
                bind:rrule={draft.rrule}
                day={draft.scheduledAt}
                name="rrule"
                tooltip={m["todos.tooltips.repeat"]()}
            />
        {/snippet}
        {#snippet color()}
            <Color
                bind:current={draft.color}
                name="color"
                tooltip={m["todos.tooltips.color"]()}
            />
        {/snippet}
        {#snippet notify()}
            <Reminder
                bind:notifyAt={draft.notifyAt}
                startsAt={draft.scheduledAt}
                name="notifyAt"
                tooltip={m["todos.tooltips.notification"]()}
                beforeOpen={() => {
                    if (!draft.hasTime) {
                        toaster.info(m["todos.reminder.select-time"]());
                        return false;
                    }
                }}
            />
        {/snippet}
        {#snippet more()}
            <RescheduleItem
                startsAt={toCalendarDate(draft.scheduledAt)}
                tooltip={m["todos.tooltips.more"]()}
                onReschedule={async (d) => {
                    if (draft.notifyAt) {
                        draft.notifyAt = draft.notifyAt.set(d);
                    }
                    draft.scheduledAt = draft.scheduledAt.set(d);

                    await tick();
                    announce(dateAnnouncerInput);
                }}
            />
        {/snippet}
    </Todo.Fields>

    <input
        bind:this={dateAnnouncerInput}
        hidden
        name="scheduledAt"
        value={draft.hasTime
            ? normalizeIsoString(draft.scheduledAt.toAbsoluteString())
            : toCalendarDate(draft.scheduledAt).toString()}
    />
</Form>
