<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Bell, CalendarFold, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { update } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";
    import Action from "./Action.svelte";
    import Color from "./Color.svelte";
    import Delete from "./Delete.svelte";

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
    <div class="flex items-center justify-between text-ms">
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
    class="absolute inset-x-0 bottom-0 z-10 flex flex-grow items-end justify-between bg-white px-4 pb-6"
>
    <Delete {todo} />
    <Action disabled tooltip={m["todos.tooltips.repeat"]()}>
        <RotateCw />
    </Action>
    <Color {todo} />
    <Action disabled tooltip={m["todos.tooltips.notification"]()}>
        <Bell />
    </Action>
    <Action tooltip={m["todos.tooltips.more"]()}><Ellipsis /></Action>
</div>
