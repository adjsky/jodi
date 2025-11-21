<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { CalendarFold } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { update } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { getLocale } from "$/paraglide/runtime";
    import { toastify } from "$/shared/inertia/toastify";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";

    type Props = {
        todo: App.Data.TodoDto;
        onClose?: VoidFunction;
    };

    const { todo, onClose }: Props = $props();
</script>

<Form
    {...toastify()}
    action={update(todo.id)}
    options={{
        only: ["todos"],
        preserveState: true,
        preserveScroll: true
    }}
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
    <Todo.Title name="title" value={todo.title} required />
    <Todo.Description name="description" value={todo.description} />
</Form>
