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
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import { fromAction } from "svelte/attachments";

    import Action from "./Action.svelte";

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
                error: m["todos.errors.edit"](),
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

    <div class="flex flex-grow items-end justify-between">
        <Action
            {@attach fromAction(inertia, () => ({
                ...baseVisitOptions,
                ...optimistic(
                    (prev) => ({
                        todos: prev.todos.filter(
                            (t: App.Data.TodoDto) => t.id == todo.id
                        )
                    }),
                    { error: m["todos.errors.delete"]() }
                ),
                href: destroy(todo.id),
                showProgress: false
            }))}
            tooltip={m["todos.tooltips.delete"]()}
        >
            <Trash />
        </Action>
        <Action disabled tooltip={m["todos.tooltips.repeat"]()}>
            <RotateCw />
        </Action>
        <Action tooltip={m["todos.tooltips.color"]()}><Circle /></Action>
        <Action disabled tooltip={m["todos.tooltips.notification"]()}>
            <Bell />
        </Action>
        <Action tooltip={m["todos.tooltips.more"]()}><Ellipsis /></Action>
    </div>
</div>
