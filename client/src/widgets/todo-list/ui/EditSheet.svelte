<script lang="ts">
    import Sheet from "#shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { TodoOccurrence } from "#entities/todo/mod.ts";

    type Props = {
        open: boolean;
        todo: TodoOccurrence | null;
        onExitComplete?: VoidFunction;
    };

    let { open = $bindable(), todo, onExitComplete }: Props = $props();
</script>

<Sheet
    bind:open
    {onExitComplete}
    maxHeight={0.9}
    snapPoints={[0.6, 1]}
    defaultSnapPoint={0.6}
>
    {#if todo}
        <EditForm {todo} onClose={() => (open = false)} />
    {/if}
</Sheet>
