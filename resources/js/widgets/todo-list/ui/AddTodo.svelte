<script lang="ts">
    import { Plus } from "@lucide/svelte";
    import { Form } from "$/features/add-todo";
    import { useHistoryModal } from "$/shared/inertia/use-history-modal.svelte";

    import Sheet from "./Sheet.svelte";

    const modal = useHistoryModal("add-todo");
</script>

<Sheet bind:open={modal.open}>
    {#snippet trigger()}
        <span
            class="flex size-8 items-center justify-center rounded-lg border border-cream-950 bg-white"
        >
            <Plus class="text-xl" />
        </span>
    {/snippet}
    {#snippet content()}<Form />{/snippet}
</Sheet>
