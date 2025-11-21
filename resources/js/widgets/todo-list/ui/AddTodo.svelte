<script lang="ts">
    import { Plus } from "@lucide/svelte";
    import { Form } from "$/features/add-todo";
    import { useHistoryModal } from "$/shared/inertia/use-history-modal.svelte";

    import Sheet from "./Sheet.svelte";

    type Props = {
        loading: boolean;
    };

    const { loading }: Props = $props();

    const modal = useHistoryModal("add-todo");
</script>

<Sheet bind:open={modal.open}>
    {#snippet trigger()}
        <button
            disabled={loading}
            tabindex={-1}
            class="flex size-8 items-center justify-center rounded-lg border border-cream-950 bg-white"
        >
            <Plus class="text-xl" />
        </button>
    {/snippet}
    {#snippet content()}<Form />{/snippet}
</Sheet>
