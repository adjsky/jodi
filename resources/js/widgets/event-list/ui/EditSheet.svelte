<script lang="ts">
    import { useLastMatching } from "$/shared/lib/hooks.svelte";
    import Sheet from "$/shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { EventData } from "$/entities/event/model/types";

    type Props = {
        open: boolean;
        event: EventData | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    const event = useLastMatching(
        () => props.event,
        (event) => event != null
    );
</script>

<Sheet
    bind:open
    maxHeight={0.9}
    snapPoints={[0.6, 1]}
    defaultSnapPoint={0.6}
    onExitComplete={() => {
        event.reset();
    }}
>
    {#if event.current}
        <EditForm event={event.current} onClose={() => (open = false)} />
    {/if}
</Sheet>
