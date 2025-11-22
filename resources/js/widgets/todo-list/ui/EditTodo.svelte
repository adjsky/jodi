<script lang="ts">
    import { Form } from "$/features/edit-todo";
    import { useHistoryModal } from "$/shared/inertia/use-history-modal.svelte";
    import { raise } from "$/shared/lib/raise";

    import Sheet from "./Sheet.svelte";

    type Props = { todo: App.Data.TodoDto | null; loading: boolean };

    let { todo = $bindable(), loading }: Props = $props();

    const modal = useHistoryModal(
        () => `edit-todo:${todo?.id}`,
        () => !loading
    );

    $effect(() => {
        if (todo != null && !modal.open) {
            modal.open = true;
        }
    });

    function close() {
        todo = null;
        modal.open = false;
    }
</script>

<Sheet
    bind:open={
        () => modal.open,
        (v) => {
            if (!v) {
                close();
            }
        }
    }
>
    {#snippet content()}
        <Form {close} todo={todo ?? raise("todo must be present")} />
    {/snippet}
</Sheet>
