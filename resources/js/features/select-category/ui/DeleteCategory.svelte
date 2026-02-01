<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/CategoryController";
    import { m } from "$/paraglide/messages";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import { raise } from "$/shared/lib/exceptions";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { view } from "../model/view";

    type Props = {
        onDelete?: (name: string) => void;
    };

    const { onDelete }: Props = $props();

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
        category: bufferedCategory ?? ""
    })}
    onConfirm={async () => {
        const category = view.meta?.__categorytodelete ?? bufferedCategory;

        if (!category) {
            raise("Can't delete when no category is marked for deletion.");
        }

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
                    error: m["todos.errors.category"](),
                    onSuccess() {
                        onDelete?.(category);
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
