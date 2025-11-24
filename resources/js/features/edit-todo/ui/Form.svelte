<script lang="ts">
    import { Form, inertia } from "@inertiajs/svelte";
    import {
        Bell,
        CalendarFold,
        Circle,
        Ellipsis,
        RotateCw,
        Trash
    } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Checkbox } from "$/features/complete-todo";
    import {
        destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { getLocale } from "$/paraglide/runtime";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";

    import type { VisitOptions } from "@inertiajs/core";

    type Props = {
        todo: App.Data.TodoDto;
        onClose?: VoidFunction;
    };

    const { todo, onClose }: Props = $props();

    const baseVisitOptions: VisitOptions = {
        only: ["todos"],
        preserveState: true,
        preserveScroll: true,
        replace: true
    };
</script>

<div class="flex h-full flex-col">
    <Form
        {...optimistic(
            (prev, data) => ({
                todos: prev.todos.map((t: App.Data.TodoDto) =>
                    t.id == todo.id
                        ? {
                              ...t,
                              ...data
                          }
                        : t
                )
            }),
            {
                error: "Failed to update todo. Try again later.",
                preserveUrl: "without-hash"
            }
        )}
        action={update(todo.id)}
        options={baseVisitOptions}
        showProgress={false}
        let:isDirty
    >
        <div class="flex items-center justify-between text-ms">
            <h4 class="flex items-center gap-1.5 font-bold text-cream-800">
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
            <Checkbox {todo} class="size-5" />
            <Todo.Title name="title" value={todo.title} required />
        </div>
        <Todo.Description name="description" value={todo.description} />
    </Form>

    <div
        class="flex flex-grow items-end justify-between *:p-3 *:text-lg *:disabled:not-data-loading:text-cream-400"
    >
        <button
            use:inertia={{
                ...baseVisitOptions,
                ...optimistic(
                    (prev) => ({
                        todos: prev.todos.filter(
                            (t: App.Data.TodoDto) => t.id == todo.id
                        )
                    }),
                    { error: "Failed to delete todo. Try again later." }
                ),
                href: destroy(todo.id),
                showProgress: false
            }}
        >
            <Trash />
        </button>
        <button disabled><RotateCw /></button>
        <button><Circle /></button>
        <button disabled><Bell /></button>
        <button><Ellipsis /></button>
    </div>
</div>
