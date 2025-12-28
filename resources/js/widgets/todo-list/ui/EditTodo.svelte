<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { parseDate } from "@internationalized/date";
    import { Bell, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { DeleteItem } from "$/features/delete-item";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import {
        destroy as _destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import Sheet from "$/shared/ui/Sheet.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        open: boolean;
        todo: App.Data.TodoDto;
    };

    let { open = $bindable(), todo }: Props = $props();
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
        let:isDirty
    >
        <Todo.Fields
            date={parseDate(todo.date.split("T")[0])}
            title={todo.title}
            description={todo.description}
            isCompleted={todo.completedAt != null}
            onCalendarOpen={() => {}}
        >
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
                <Category name="category" defaultValue={todo.category} />
            {/snippet}
            {#snippet checkbox()}
                <Checkbox {todo} class="size-6 text-lg" />
            {/snippet}
            {#snippet destroy()}
                <DeleteItem
                    title={m["todos.delete-ahtung"]()}
                    tooltip={m["todos.tooltips.delete"]()}
                    href={_destroy(todo.id)}
                    {...visitOptions}
                    {...optimistic.delete(todo.id)}
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
                    {...optimistic.edit(todo.id, true)}
                    href={update(todo.id)}
                    tooltip={m["todos.tooltips.color"]()}
                    current={todo.color}
                    preserveUrl
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
