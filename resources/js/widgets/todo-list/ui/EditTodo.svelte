<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        toCalendarDate
    } from "@internationalized/date";
    import { Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { DeleteItem } from "$/features/delete-item";
    import { YearCalendarDialog } from "$/features/filter-by-date";
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
    import Sheet from "$/shared/ui/Sheet.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { watch } from "runed";
    import { tick } from "svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        open: boolean;
        todo: App.Data.TodoDto | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    let dateAnnouncerInput: HTMLInputElement | null = $state(null);

    // --------------------------- OVERRIDES -----------------------------------

    let scheduledAtOverride = $state<ZonedDateTime | null>(null);
    let hasTimeOverride = $state<boolean | null>(null);
    let notifyAtOverride = $state<ZonedDateTime | null | undefined>(undefined);

    function resetOverrides() {
        scheduledAtOverride = null;
        hasTimeOverride = null;
        notifyAtOverride = undefined;
    }

    // --------------------------- TODO DATA -----------------------------------

    let lastKnownTodo = $state(props.todo);
    let todo = $derived(props.todo ?? (lastKnownTodo as App.Data.TodoDto));

    let scheduledAt = $derived(
        scheduledAtOverride ?? parseAbsoluteToLocal(todo.scheduledAt)
    );
    let hasTime = $derived(hasTimeOverride ?? todo.hasTime);
    let notifyAt = $derived.by(() => {
        if (notifyAtOverride !== undefined) {
            return notifyAtOverride;
        }

        if (!todo.notifyAt) {
            return null;
        }

        return parseAbsoluteToLocal(todo.notifyAt);
    });

    watch(
        () => [props.todo],
        () => {
            if (!props.todo) return;
            lastKnownTodo = props.todo;
        }
    );
</script>

<Sheet
    bind:open
    maxHeight={0.9}
    snapPoints={[0.6]}
    startingSnapPoint={0.6}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
    onCloseComplete={() => {
        resetOverrides();
    }}
>
    <Form
        {...optimistic.edit(todo.id)}
        action={update(todo.id)}
        options={visitOptions}
        showProgress={false}
        transform={(data) => ({
            ...cleanFormPayload(data),
            hasTime,
            scheduledAt: hasTime
                ? normalizeIsoString(scheduledAt.toAbsoluteString())
                : toCalendarDate(scheduledAt).toString()
        })}
        onSuccess={() => {
            if (notifyAt) {
                PushSubscription.ahtung(m["todos.reminder-ahtung"]());
            }
        }}
        class="flex grow flex-col pb-18"
        let:isDirty
    >
        <!-- keep to mark form dirty when selecting date in calendar -->
        <input
            bind:this={dateAnnouncerInput}
            hidden
            name="_date"
            value={toCalendarDate(scheduledAt).toString()}
        />
        <Todo.Fields
            {scheduledAt}
            title={todo.title}
            description={todo.description}
            isCompleted={todo.completedAt != null}
        >
            {#snippet calendar(trigger)}
                <YearCalendarDialog
                    selected={toCalendarDate(scheduledAt)}
                    onSelect={async (d) => {
                        if (notifyAt) {
                            notifyAtOverride = notifyAt.set(d);
                        }
                        scheduledAtOverride = scheduledAt.set(d);
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
                            open = false;
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
                    {scheduledAt}
                    bind:hasTime={() => hasTime, (v) => (hasTimeOverride = v)}
                    onChange={(time, hasTime) => {
                        if (!hasTime) {
                            notifyAtOverride = null;
                        } else {
                            if (notifyAt) {
                                notifyAtOverride = notifyAt.add(
                                    diff(scheduledAt, time)
                                );
                            } else {
                                notifyAtOverride = scheduledAt
                                    .set(time)
                                    .subtract({
                                        hours: NOTIFICATION_DEFAULT_SUBHOURS
                                    });
                            }
                        }

                        scheduledAtOverride = scheduledAt.set(time);
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
                    name="color"
                    tooltip={m["todos.tooltips.color"]()}
                    current={todo.color}
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
</Sheet>
