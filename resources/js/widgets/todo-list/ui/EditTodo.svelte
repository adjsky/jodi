<script lang="ts">
    import { Form } from "$/features/edit-todo";

    import TodoSheet from "./TodoSheet.svelte";

    type Props = {
        todo: App.Data.TodoDto | null;
        onclose?: VoidFunction;
    };

    let { todo, onclose }: Props = $props();
</script>

<TodoSheet
    bind:open={
        () => todo != null,
        () => {
            onclose?.();
        }
    }
>
    {#snippet content()}
        <Form onClose={onclose} todo={todo!} />
    {/snippet}
</TodoSheet>
