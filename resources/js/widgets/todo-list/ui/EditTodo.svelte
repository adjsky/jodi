<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { DateFormatter } from "@internationalized/date";
    import { Bell, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { DeleteItem } from "$/features/delete-item";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import {
        destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
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
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h4 class="text-lg font-bold">
                    {new DateFormatter(getLocale(), {
                        day: "2-digit",
                        year: "numeric",
                        month: "short",
                        weekday: "short"
                    }).format(new Date(todo.date))}
                </h4>
                <Category name="category" defaultValue={todo.category} />
            </div>
            <SaveOrClose
                variant={isDirty ? "save" : "close"}
                onclick={() => {
                    if (!isDirty) {
                        open = false;
                    }
                }}
            />
        </div>
        <div
            class={[
                "mt-4 flex items-center gap-2",
                todo.completedAt && "opacity-40"
            ]}
        >
            <Checkbox {todo} class="size-6 text-lg" />
            <Todo.Title name="title" defaultValue={todo.title} required />
        </div>
        <Todo.Description
            name="description"
            defaultValue={todo.description ?? ""}
            class="mt-6"
        />
    </Form>

    <div
        class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between bg-white px-4 pb-6"
    >
        <DeleteItem
            title={m["todos.delete-ahtung"]()}
            tooltip={m["todos.tooltips.delete"]()}
            href={destroy(todo.id)}
            {...visitOptions}
            {...optimistic.delete(todo.id)}
        />
        <ToolbarAction disabled tooltip={m["todos.tooltips.repeat"]()}>
            <RotateCw />
        </ToolbarAction>
        <Color
            {...visitOptions}
            {...optimistic.edit(todo.id, true)}
            href={update(todo.id)}
            tooltip={m["todos.tooltips.color"]()}
            current={todo.color}
            preserveUrl
        />
        <ToolbarAction disabled tooltip={m["todos.tooltips.notification"]()}>
            <Bell />
        </ToolbarAction>
        <ToolbarAction disabled tooltip={m["todos.tooltips.more"]()}>
            <Ellipsis />
        </ToolbarAction>
    </div>
</Sheet>
