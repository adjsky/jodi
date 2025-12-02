<script lang="ts">
    import { Form } from "$/features/edit-todo";
    import { useHistoryModal } from "$/shared/inertia/use-history-modal.svelte";
    import { raise } from "$/shared/lib/raise";
    import { watch } from "runed";

    import TodoSheet from "./TodoSheet.svelte";

    type Props = {
        todo: App.Data.TodoDto | null;
        open: boolean;
        loading: boolean;
    };

    let { todo, open = $bindable(), loading }: Props = $props();

    const modal = useHistoryModal(
        () => `edit-todo:${todo?.id}`,
        () => !loading
    );

    watch(
        () => [open],
        () => {
            if (open) {
                modal.open = true;
            }
        },
        { lazy: true }
    );

    watch(
        () => [modal.open],
        () => {
            if (!modal.open) {
                open = false;
            }
        },
        { lazy: true }
    );
</script>

<TodoSheet bind:open={modal.open}>
    {#snippet content()}
        <Form
            onClose={() => (modal.open = false)}
            todo={todo ?? raise("todo must be present")}
        />
    {/snippet}
</TodoSheet>
