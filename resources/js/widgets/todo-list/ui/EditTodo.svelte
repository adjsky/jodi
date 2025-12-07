<script lang="ts">
    import { Form } from "$/features/edit-todo";

    import TodoSheet from "./TodoSheet.svelte";

    type Props = {
        open: boolean;
        todo: App.Data.TodoDto | null;
    };

    let { open = $bindable(), todo }: Props = $props();
</script>

<TodoSheet bind:open>
    <Form
        todo={todo ?? ({} as App.Data.TodoDto)}
        onclose={() => (open = false)}
    />
</TodoSheet>
