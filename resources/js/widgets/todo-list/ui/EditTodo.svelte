<script lang="ts">
    import { Form } from "$/features/edit-todo";
    import { useHistoryModal } from "$/shared/inertia/use-history-modal.svelte";
    import { raise } from "$/shared/lib/raise";
    import { watch } from "runed";

    import Sheet from "./Sheet.svelte";

    type Props = { todo: App.Data.TodoDto | null; loading: boolean };

    let { todo = $bindable(), loading }: Props = $props();

    const modal = useHistoryModal(
        () => `edit-todo:${todo?.id}`,
        () => !loading
    );

    watch(
        () => [todo],
        () => {
            modal.open = todo != null;
        }
    );
</script>

<Sheet bind:open={modal.open}>
    {#snippet content()}
        <Form
            onClose={() => (modal.open = false)}
            todo={todo ?? raise("todo must be present")}
        />
    {/snippet}
</Sheet>
