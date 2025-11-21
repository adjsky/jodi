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
    import {
        destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { getLocale } from "$/paraglide/runtime";
    import { toastify } from "$/shared/inertia/toastify";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";

    import type { VisitOptions } from "@inertiajs/core";

    type Props = {
        todo: App.Data.TodoDto;
        onClose?: VoidFunction;
    };

    const { todo, onClose }: Props = $props();

    const baseVisitOptions: VisitOptions = {
        only: ["todos", "flash"],
        preserveState: true,
        preserveScroll: true,
        replace: true
    };
</script>

<div class="flex h-full flex-col">
    <Form
        {...toastify()}
        action={update(todo.id)}
        options={baseVisitOptions}
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
        <div class="mt-5 flex items-center gap-2">
            <button
                aria-label="lorem"
                class="size-5 shrink-0 rounded-full border border-cream-950"
            ></button>
            <Todo.Title name="title" value={todo.title} required />
        </div>
        <Todo.Description name="description" value={todo.description} />
    </Form>

    <div
        class="flex flex-grow items-end justify-between *:p-3 *:text-lg *:disabled:text-cream-400"
    >
        <button
            use:inertia={{
                ...baseVisitOptions,
                ...toastify(),
                href: destroy(todo.id)
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
