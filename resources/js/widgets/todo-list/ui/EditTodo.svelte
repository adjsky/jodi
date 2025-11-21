<script lang="ts">
    import { Form } from "$/features/edit-todo";
    import { useHistoryModal } from "$/shared/inertia/use-history-modal.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";

    import Sheet from "./Sheet.svelte";

    type Props = { todo: App.Data.TodoDto; loading: boolean };

    const { todo, loading }: Props = $props();

    const modal = useHistoryModal(`edit-todo:${todo.id}`, () => !loading);
</script>

<Sheet bind:open={modal.open}>
    {#snippet trigger()}
        <button
            tabindex={-1}
            disabled={loading}
            class="relative table w-full table-fixed text-start text-ms font-medium"
        >
            {#if loading}
                <Skeleton inline style="width: {Math.random() * 100 + 100}px" />
            {:else}
                <span
                    class="table-cell overflow-hidden text-ellipsis whitespace-nowrap"
                >
                    {todo.title}
                </span>
            {/if}
        </button>
    {/snippet}
    {#snippet content()}
        <Form {todo} onClose={() => (modal.open = false)} />
    {/snippet}
</Sheet>

<style>
    button::after {
        content: "";

        position: absolute;
        left: 0;
        bottom: -4px;

        width: 100%;
        height: 1px;
        border-radius: 1px;

        background: var(--color-cream-200);
    }
</style>
