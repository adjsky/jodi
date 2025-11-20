<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { CalendarFold } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { add } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { getLocale } from "$/paraglide/runtime";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import dayjs from "dayjs";

    const searchParams = useSearchParams();
    const day = $state(dayjs(searchParams["d"]).locale(getLocale()));
</script>

<Form action={add()}>
    <input name="todo_date" value={day.format("YYYY-MM-DD")} hidden />
    <div class="flex items-center justify-between">
        <h4 class="flex items-center gap-1.5 text-ms font-bold text-cream-800">
            <CalendarFold />
            {new Intl.DateTimeFormat(day.locale(), {
                day: "2-digit",
                year: "numeric",
                month: "short",
                weekday: "short"
            }).format(day.toDate())}
        </h4>
        <button class="text-ms font-bold text-brand">Сохранить</button>
    </div>
    <Todo.Title name="title" required />
    <Todo.Description name="description" />
</Form>
