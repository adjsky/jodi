<script lang="ts">
    import { router } from "@inertiajs/core";
    import { useQueryClient } from "@tanstack/svelte-query";
    import DestroyCategory from "$/generated/actions/App/Domain/Todo/Actions/DestroyCategory";
    import { m } from "$/paraglide/messages";
    import { optimistic } from "$/shared/integrations/inertia";
    import { raise } from "$/shared/lib/exception/raise";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { categoriesQueryOptions } from "../api/categories";
    import { view } from "../model/view";

    import type { CategoryData, TodoData } from "$/entities/todo";

    const queryClient = useQueryClient();

    let category: CategoryData | null = $state(
        view.meta?.__categorytodelete ?? null
    );

    $effect(() => {
        const c = view.meta?.__categorytodelete;
        if (c) category = c;
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
        category: category?.name ?? ""
    })}
    onConfirm={async () => {
        if (!category) {
            raise("No category available for deletion.");
        }

        const categoryId = category.id;

        // This should resolve immediately, so it is safe to use.
        await queryClient.cancelQueries({
            queryKey: categoriesQueryOptions.queryKey
        });

        const previousCategories = queryClient.getQueryData(
            categoriesQueryOptions.queryKey
        );

        queryClient.setQueryData(
            categoriesQueryOptions.queryKey,
            (categories) => categories?.filter((c) => c.id != categoryId)
        );

        router.visit(DestroyCategory(categoryId), {
            ...optimistic<{ todos: TodoData[] }>(
                (prev) => ({
                    todos: prev.todos.map((t) => ({
                        ...t,
                        category:
                            t.category?.id == categoryId ? null : t.category
                    }))
                }),
                {
                    rollbackError: m["todos.errors.delete-category"](),
                    onOptimisticRollback() {
                        queryClient.setQueryData(
                            categoriesQueryOptions.queryKey,
                            previousCategories
                        );
                    }
                }
            ),
            only: ["todos"],
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            replace: true,
            showProgress: false
        });
    }}
/>
