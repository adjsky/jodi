<script lang="ts">
    import { Form, router } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        toCalendarDate
    } from "@internationalized/date";
    import { RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { DeleteItem } from "$/features/delete-item";
    import { daySummary, YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { TodoTime } from "$/features/schedule-todo-time";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import { Reminder } from "$/features/select-reminder";
    import {
        destroy as _destroy,
        complete,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { NOTIFICATION_DEFAULT_SUBHOURS } from "$/shared/cfg/constants";
    import { diff, normalizeIsoString } from "$/shared/lib/date";
    import { announce, cleanFormPayload } from "$/shared/lib/form";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { toaster } from "$/shared/lib/toaster";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { watch } from "runed";
    import { tick, untrack } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        todo: App.Data.TodoDto | null;
        onClose?: VoidFunction;
    };

    const { onClose, ...props }: Props = $props();

    let dateAnnouncerInput: HTMLInputElement | null = $state(null);
    let lastKnownTodo = $state(props.todo);

    let todo = $derived(props.todo ?? (lastKnownTodo as App.Data.TodoDto));

    const draft = $state(
        untrack(() => ({
            scheduledAt: parseAbsoluteToLocal(todo.scheduledAt),
            hasTime: todo.hasTime,
            notifyAt: todo.notifyAt
                ? parseAbsoluteToLocal(todo.notifyAt)
                : null,
            color: todo.color
        }))
    );

    watch(
        () => [props.todo],
        () => {
            if (!props.todo) return;
            lastKnownTodo = props.todo;
        }
    );
</script>

<Form
    {...optimistic.edit(todo.id)}
    action={update(todo.id)}
    options={visitOptions}
    showProgress={false}
    transform={(data) => ({
        ...cleanFormPayload(data),
        hasTime: draft.hasTime,
        scheduledAt: draft.hasTime
            ? normalizeIsoString(draft.scheduledAt.toAbsoluteString())
            : toCalendarDate(draft.scheduledAt).toString()
    })}
    onSuccess={() => {
        if (draft.notifyAt) {
            PushSubscription.ahtung(m["todos.reminder-ahtung"]());
        }
        router.flushByCacheTags("week-carousel");
        daySummary.flush();
    }}
    class="flex grow flex-col pb-18"
    let:isDirty
>
    <!-- keep to mark form dirty when selecting date in calendar -->
    <input
        bind:this={dateAnnouncerInput}
        hidden
        name="_date"
        value={toCalendarDate(draft.scheduledAt).toString()}
    />
    <Todo.Fields
        scheduledAt={draft.scheduledAt}
        title={todo.title}
        description={todo.description}
        isCompleted={todo.completedAt != null}
    >
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(draft.scheduledAt)}
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
                variant={isDirty ? "save" : "close"}
                onclick={() => {
                    if (!isDirty) {
                        onClose?.();
                    }
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
                href={complete(todo.id)}
                completedAt={todo.completedAt}
                class="size-6 text-lg"
            />
        {/snippet}
        {#snippet time()}
            <TodoTime
                bind:hasTime={draft.hasTime}
                scheduledAt={draft.scheduledAt}
                onChange={(time, hasTime) => {
                    if (!hasTime) {
                        draft.notifyAt = null;
                    } else if (draft.notifyAt) {
                        draft.notifyAt = draft.notifyAt.add(
                            diff(draft.scheduledAt, time)
                        );
                    } else {
                        draft.notifyAt = draft.scheduledAt.set(time).subtract({
                            hours: NOTIFICATION_DEFAULT_SUBHOURS
                        });
                    }

                    draft.scheduledAt = draft.scheduledAt.set(time);
                }}
            />
        {/snippet}
        {#snippet destroy()}
            <DeleteItem
                {...visitOptions}
                {...optimistic.delete(todo.id)}
                href={_destroy(todo.id)}
                title={m["todos.delete-ahtung"]()}
                tooltip={m["todos.tooltips.delete"]()}
            />
        {/snippet}
        {#snippet repeat()}
            <ToolbarAction disabled tooltip={m["todos.tooltips.repeat"]()}>
                <RotateCw />
            </ToolbarAction>
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
                onReschedule={(d) => {
                    if (draft.notifyAt) {
                        draft.notifyAt = draft.notifyAt.set(d);
                    }
                    draft.scheduledAt = draft.scheduledAt.set(d);
                }}
            />
        {/snippet}
    </Todo.Fields>
</Form>
