<script lang="ts">
    import Sheet from "$/shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { EventData } from "$/entities/event/model/types";

    type Props = {
        open: boolean;
        event: EventData | null;
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
