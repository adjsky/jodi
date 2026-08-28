<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { useQueryClient } from "@tanstack/svelte-query";
    import DestroyCategory from "$/generated/actions/App/Domain/Todo/Actions/DestroyCategory";
    import { m } from "$/paraglide/messages";
    import { optimistic } from "$/shared/integrations/inertia";
    import { raise } from "$/shared/lib/exception/raise";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { categoriesQueryOptions } from "../api/categories";
    import { view } from "../model/view";

    import type { CategoryData, TodoData } from "$/entities/todo";

    type Props = {
        onDelete?: (id: number) => void;
    };

    const { onDelete }: Props = $props();

    const queryClient = useQueryClient();

    let bufferedCategory = $state<CategoryData | null>(null);

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
        category: bufferedCategory?.name ?? ""
    })}
    onConfirm={async () => {
        const category = view.meta?.__categorytodelete ?? bufferedCategory;

        if (!category) {
            raise("Can't delete when no category is marked for deletion.");
        }

        let previousCategories: CategoryData[] | null = null;

        void router.visit(DestroyCategory(category.id), {
            ...optimistic<{ todos: TodoData[] }>(
                (prev) => ({
                    todos: prev.todos.map((t: TodoData) => ({
                        ...t,
                        category:
                            t.category?.id == category.id ? null : t.category
                    }))
                }),
                {
                    error: m["todos.errors.category"](),
                    onBefore() {
                        void queryClient.cancelQueries({
                            queryKey: categoriesQueryOptions.queryKey
                        });

                        previousCategories =
                            queryClient.getQueryData(
                                categoriesQueryOptions.queryKey
                            ) ?? null;

                        queryClient.setQueryData(
                            categoriesQueryOptions.queryKey,
                            (categories) =>
                                categories?.filter((c) => c.id != category.id)
                        );
                    },
                    onOptimisticRollback() {
                        if (!previousCategories) return;

                        queryClient.setQueryData(
                            categoriesQueryOptions.queryKey,
                            previousCategories
                        );
                    },
                    onSuccess() {
                        previousCategories = null;
                        onDelete?.(category.id);
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

        return true;
    }}
/>
