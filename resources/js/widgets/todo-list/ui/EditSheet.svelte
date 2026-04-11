<script lang="ts">
    import Sheet from "$/shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { TodoData } from "$/entities/todo";

    type Props = {
        open: boolean;
        todo: TodoData | null;
    };

    let { open = $bindable(), todo }: Props = $props();
</script>

<Sheet
    bind:open
    maxHeight={0.9}
    snapPoints={[0.6]}
    startingSnapPoint={0.6}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    <EditForm {todo} onClose={() => (open = false)} />
</Sheet>
