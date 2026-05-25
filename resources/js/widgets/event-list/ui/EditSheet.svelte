<script lang="ts">
    import Sheet from "$/shared/ui/Sheet.svelte";
    import { untrack } from "svelte";

    import EditForm from "./EditForm.svelte";

    import type { EventData } from "$/entities/event/model/types";

    type Props = {
        open: boolean;
        event: EventData | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    let lastEvent = $state(untrack(() => props.event));

    $effect(() => {
        if (!props.event) return;
        lastEvent = props.event;
    });

    const event = $derived(props.event ?? lastEvent);
</script>

<Sheet
    bind:open
    maxHeight={0.9}
    snapPoints={[0.6, 1]}
    defaultSnapPoint={0.6}
    onExitComplete={() => {
        lastEvent = null;
    }}
>
    {#if event}
        <EditForm {event} onClose={() => (open = false)} />
    {/if}
</Sheet>
