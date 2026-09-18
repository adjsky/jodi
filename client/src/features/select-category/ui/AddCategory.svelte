<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Plus } from "@lucide/svelte";
    import CreateCategory from "$/generated/actions/App/Domain/Todo/Actions/CreateCategory";
    import { m } from "$/paraglide/messages";
    import { useLoadingDebounce } from "$/shared/lib/svelte/use-loading-debounce.svelte";
    import Loader from "$/shared/ui/Loader.svelte";

    import type { CategoryData } from "$/entities/todo";

    type Props = {
        name: string;
        onAdd?: (category: CategoryData) => void;
    };

    const { name, onAdd }: Props = $props();

    let isAdding = $state(false);
    const isLoaderVisible = useLoadingDebounce(() => isAdding);
</script>

<Form
    action={CreateCategory()}
    options={{
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        replace: true
    }}
    showProgress={false}
    onStart={() => {
        isAdding = true;
    }}
    onSuccess={(page) => {
        onAdd?.(page.flash.category);
    }}
    onFinish={() => {
        isAdding = false;
    }}
>
    <input hidden name="name" value={name} />
    <button
        type="submit"
        disabled={isAdding}
        class="flex h-14 w-full items-center gap-2 rounded-xl bg-brand/10 px-2 text-start text-lg font-medium disabled:cursor-not-allowed"
        aria-busy={isAdding}
    >
        {#if isLoaderVisible.current}
            <Loader class="mx-auto text-brand" />
        {:else}
            <span
                class="flex size-7 items-center justify-center rounded-full bg-brand"
            >
                <Plus class="text-xl text-white" />
            </span>
            {m["todos.category.add"]()}
        {/if}
    </button>
</Form>
