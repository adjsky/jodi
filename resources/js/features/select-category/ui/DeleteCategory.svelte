<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import DestroyCategory from "$/generated/actions/App/Domain/Todo/Actions/DestroyCategory";
    import { m } from "$/paraglide/messages";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import { raise } from "$/shared/lib/exceptions";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { view } from "../model/view";

    import type { CategoryData, TodoData } from "$/entities/todo";

    type Props = {
        onDelete?: (id: number) => void;
    };

    const { onDelete }: Props = $props();

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

        void router.visit(DestroyCategory(category.id), {
            ...optimistic(
                (prev) => ({
                    todos: prev.todos.map((t: TodoData) => ({
                        ...t,
                        category:
                            t.category?.id == category.id ? null : t.category
                    })),
                    categories: prev.categories.filter(
                        (c: CategoryData) => c.id != category.id
                    )
                }),
                {
                    error: m["todos.errors.category"](),
                    onSuccess() {
                        onDelete?.(category.id);
                    }
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
