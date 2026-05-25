<script lang="ts">
    import Sheet from "$/shared/ui/Sheet.svelte";
    import { untrack } from "svelte";

    import EditForm from "./EditForm.svelte";

    import type { TodoData } from "$/entities/todo";

    type Props = {
        open: boolean;
        todo: TodoData | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    let lastTodo = $state(untrack(() => props.todo));

    $effect(() => {
        if (!props.todo) return;
        lastTodo = props.todo;
    });

    const todo = $derived(props.todo ?? lastTodo);
</script>

<Sheet bind:open maxHeight={0.9} snapPoints={[0.6, 1]} defaultSnapPoint={0.6}>
    {#if todo}
        <EditForm {todo} onClose={() => (open = false)} />
    {/if}
</Sheet>
