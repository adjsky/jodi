<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Bell, CalendarFold, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import Action from "$/features/_shared/action-toolbar/Action.svelte";
    import Color from "$/features/_shared/action-toolbar/Color.svelte";
    import Delete from "$/features/_shared/action-toolbar/Delete.svelte";
    import { Checkbox } from "$/features/complete-todo";
    import {
        destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        todo: App.Data.TodoDto;
        onclose?: VoidFunction;
    };

    const { todo, onclose }: Props = $props();
</script>

<Form
    {...optimistic.edit(todo.id)}
    action={update(todo.id)}
    options={visitOptions}
    showProgress={false}
    let:isDirty
>
    <div class="flex items-center justify-between">
        <h4 class="flex items-center gap-1.5 text-lg font-bold text-cream-800">
            <CalendarFold />
            {new Intl.DateTimeFormat(getLocale(), {
                day: "2-digit",
                year: "numeric",
                month: "short",
                weekday: "short"
            }).format(new Date(todo.date))}
        </h4>
        <SaveOrClose
            variant={isDirty ? "save" : "close"}
            onclick={() => {
                if (!isDirty) {
                    onclose?.();
                }
            }}
        />
    </div>
    <div
        class={[
            "mt-5 flex items-center gap-2",
            todo.completedAt && "opacity-40"
        ]}
    >
        <Checkbox {todo} class="size-6 text-lg" />
        <Todo.Title name="title" defaultValue={todo.title} required />
    </div>
    <Todo.Description
        name="description"
        defaultValue={todo.description ?? ""}
    />
</Form>

<div
    class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between bg-white px-4 pb-6"
>
    <Delete
        title={m["todos.delete-ahtung"]()}
        tooltip={m["todos.tooltips.delete"]()}
        href={destroy(todo.id)}
        {...visitOptions}
        {...optimistic.delete(todo.id)}
    />
    <Action disabled tooltip={m["todos.tooltips.repeat"]()}>
        <RotateCw />
    </Action>
    <Color
        {...visitOptions}
        {...optimistic.edit(todo.id, true)}
        href={update(todo.id)}
        tooltip={m["todos.tooltips.color"]()}
        current={todo.color}
        preserveUrl
    />
    <Action disabled tooltip={m["todos.tooltips.notification"]()}>
        <Bell />
    </Action>
    <Action disabled tooltip={m["todos.tooltips.more"]()}><Ellipsis /></Action>
</div>
