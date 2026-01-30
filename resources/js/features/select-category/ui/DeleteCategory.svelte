<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/CategoryController";
    import { m } from "$/paraglide/messages";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { view } from "../model/view";

    let bufferedCategory = $state<string | null>(null);

    $effect(() => {
        const category = view.meta?.__categorytodelete;
        if (category) {
            bufferedCategory = category;
        }
    });
</script>

<Confirmable
    bind:open={
        () => view.meta?.__categorytodelete != undefined,
        async (v) => {
            if (!v) {
                void view.back();
            }
        }
    }
    title={m["todos.category.confirm-delete"]({
        category: bufferedCategory ?? "<>"
    })}
    onConfirm={async () => {
        const category = view.meta!.__categorytodelete;

        void router.visit(destroy(category), {
            ...optimistic(
                (prev) => ({
                    todos: prev.todos.map((t: App.Data.TodoDto) => ({
                        ...t,
                        category: t.category == category ? null : t.category
                    })),
                    categories: prev.categories.filter(
                        (c: string) => c != category
                    )
                }),
                {
                    error: m["todos.errors.category"]()
                }
            ),
            only: ["todos", "categories"],
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            replace: true,
            showProgress: false
        });

        return true;
    }}
/>
