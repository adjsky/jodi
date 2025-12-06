<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Bell, CalendarFold, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import { update } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";

    import { optimisticEdit, visitOptions } from "../cfg/inertia";
    import Action from "./Action.svelte";
    import Color from "./Color.svelte";
    import Delete from "./Delete.svelte";

    type Props = {
        todo: App.Data.TodoDto;
        onClose?: VoidFunction;
    };

    const { todo, onClose }: Props = $props();
</script>

<div class="flex h-full flex-col">
    <Form
        {...optimisticEdit(todo.id)}
        action={update(todo.id)}
        options={visitOptions}
        showProgress={false}
        let:isDirty
    >
        <div class="flex items-center justify-between text-ms">
            <h4
                class="flex items-center gap-1.5 text-lg font-bold text-cream-800"
            >
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
                        onClose?.();
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
            <Todo.Title name="title" value={todo.title} required />
        </div>
        <Todo.Description name="description" value={todo.description} />
    </Form>

    <div class="flex flex-grow items-end justify-between">
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
</div>
