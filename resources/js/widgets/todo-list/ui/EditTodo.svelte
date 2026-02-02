<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        parseAbsoluteToLocal,
        toCalendarDate
    } from "@internationalized/date";
    import { Bell, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { DeleteItem } from "$/features/delete-item";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { TodoTime } from "$/features/schedule-todo-time";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import {
        destroy as _destroy,
        complete,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { normalizeIsoString } from "$/shared/lib/date";
    import { announce, cleanFormPayload } from "$/shared/lib/form";
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

    let lastKnownTodo = $state(props.todo);
    let scheduledAtOverride = $state<ZonedDateTime | null>(null);
    let dateAnnouncerInput: HTMLInputElement | null = $state(null);

    let todo = $derived(props.todo ?? (lastKnownTodo as App.Data.TodoDto));
    let scheduledAt = $derived(
        scheduledAtOverride ?? parseAbsoluteToLocal(todo.scheduledAt)
    );

    watch(
        () => [props.todo],
        () => {
            if (props.todo?.id != lastKnownTodo?.id) {
                scheduledAtOverride = null;
            }

            if (!props.todo) return;
            lastKnownTodo = props.todo;
        }
    );
</script>

<Sheet
    bind:open
    defaultSnapPoint={0.6}
    snapPoints={[0.6, 0.95]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    <Form
        {...optimistic.edit(todo.id)}
        action={update(todo.id)}
        options={visitOptions}
        showProgress={false}
        transform={(data) => ({
            ...cleanFormPayload(data),
            scheduledAt: normalizeIsoString(scheduledAt.toAbsoluteString())
        })}
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
                    bind:scheduledAt={
                        () => scheduledAt,
                        (t) => (scheduledAtOverride = scheduledAt.set(t))
                    }
                    hasTime={todo.hasTime}
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
                    {...visitOptions}
                    {...optimistic.edit(todo.id, false)}
                    href={update(todo.id)}
                    tooltip={m["todos.tooltips.color"]()}
                    current={todo.color}
                />
            {/snippet}
            {#snippet notify()}
                <ToolbarAction
                    disabled
                    tooltip={m["todos.tooltips.notification"]()}
                >
                    <Bell />
                </ToolbarAction>
            {/snippet}
            {#snippet more()}
                <ToolbarAction disabled tooltip={m["todos.tooltips.more"]()}>
                    <Ellipsis />
                </ToolbarAction>
            {/snippet}
        </Todo.Fields>
    </Form>
</Sheet>
