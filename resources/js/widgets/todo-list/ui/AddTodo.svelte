<script lang="ts">
    import { Plus } from "@lucide/svelte";
    import { Form } from "$/features/add-todo";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";

    import TodoSheet from "./TodoSheet.svelte";

    type Props = {
        loading: boolean;
    };

    const { loading }: Props = $props();

    const view = new HistoryView("add-todo");
</script>

<TodoSheet
    bind:open={
        () => view.isOpen(),
        (v) => {
            if (v) {
                view.open();
            } else {
                view.close();
            }
        }
    }
>
    {#snippet trigger()}
        <button
            disabled={loading}
            tabindex={-1}
            class="flex size-9 items-center justify-center rounded-full border border-cream-950 bg-white"
        >
            <Plus class="text-xl" />
        </button>
    {/snippet}
    {#snippet content()}<Form />{/snippet}
</TodoSheet>
