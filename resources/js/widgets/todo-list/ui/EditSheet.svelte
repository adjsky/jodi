<script lang="ts">
    import { useLastDefined } from "$/shared/lib/hooks.svelte";
    import Sheet from "$/shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { TodoData } from "$/entities/todo";

    type Props = {
        open: boolean;
        todo: TodoData | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    const todo = useLastDefined(() => props.todo);
</script>

<Sheet
    bind:open
    maxHeight={0.9}
    snapPoints={[0.6, 1]}
    defaultSnapPoint={0.6}
    onExitComplete={() => {
        todo.reset();
    }}
>
    {#if todo.current}
        <EditForm todo={todo.current} onClose={() => (open = false)} />
    {/if}
</Sheet>
