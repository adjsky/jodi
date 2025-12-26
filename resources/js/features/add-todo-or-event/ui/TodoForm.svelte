<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Todo } from "$/entities/todo";
    import { Category } from "$/features/select-category";
    import { create } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { getLocale } from "$/paraglide/runtime";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import dayjs from "dayjs";

    const searchParams = useSearchParams();
    const day = $state(dayjs(searchParams["d"]));
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
    <input name="todoDate" value={day.format("YYYY-MM-DD")} hidden />
    <div class="flex items-center justify-between text-ms">
        <div class="flex items-center gap-3">
            <h4 class="text-lg font-bold">
                {new Intl.DateTimeFormat(getLocale(), {
                    day: "2-digit",
                    year: "numeric",
                    month: "short",
                    weekday: "short"
                }).format(new Date(day.toDate()))}
            </h4>
            <Category name="category" />
        </div>
        <SaveOrClose variant="save" disabled={processing} />
    </div>
    <Todo.Title class="mt-5" name="title" required autofocus />
    <Todo.Description name="description" class="mt-6" />
</Form>
