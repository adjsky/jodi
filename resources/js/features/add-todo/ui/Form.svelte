<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { CalendarFold } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { create } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { getLocale } from "$/paraglide/runtime";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import dayjs from "dayjs";

    const searchParams = useSearchParams();
    const day = $state(dayjs(searchParams["d"]).locale(getLocale()));
</script>

<Form
    action={create()}
    options={{
        only: ["todos"],
        preserveState: true,
        preserveScroll: true,
        replace: true
    }}
    let:processing
>
    <input name="todo_date" value={day.format("YYYY-MM-DD")} hidden />
    <div class="flex items-center justify-between text-ms">
        <h4 class="flex items-center gap-1.5 text-lg font-bold text-cream-800">
            <CalendarFold />
            {new Intl.DateTimeFormat(day.locale(), {
                day: "2-digit",
                year: "numeric",
                month: "short",
                weekday: "short"
            }).format(day.toDate())}
        </h4>
        <SaveOrClose variant="save" disabled={processing} />
    </div>
    <Todo.Title class="mt-5" name="title" required autofocus />
    <Todo.Description name="description" />
</Form>
