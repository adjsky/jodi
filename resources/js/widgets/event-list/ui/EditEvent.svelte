<script lang="ts">
    import { Form } from "$/features/edit-todo";
    import Sheet from "$/shared/ui/Sheet.svelte";

    type Props = {
        open: boolean;
        event: App.Data.EventDto | null;
    };

    let { open = $bindable(), event }: Props = $props();
</script>

<Sheet
    bind:open
    defaultSnapPoint={0.6}
    snapPoints={[0.6, 0.95]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    <Form
        todo={todo ?? ({} as App.Data.TodoDto)}
        onclose={() => (open = false)}
    />
</Sheet>
