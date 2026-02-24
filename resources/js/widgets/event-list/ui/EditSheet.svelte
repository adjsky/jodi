<script lang="ts">
    import Sheet from "$/shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    type Props = {
        open: boolean;
        event: App.Data.EventDto | null;
    };

    let { open = $bindable(), event }: Props = $props();
</script>

<Sheet
    bind:open
    maxHeight={0.9}
    snapPoints={[0.6]}
    startingSnapPoint={0.6}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    <EditForm {event} onClose={() => (open = false)} />
</Sheet>
