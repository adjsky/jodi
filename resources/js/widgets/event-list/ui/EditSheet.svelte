<script lang="ts">
    import { useLastDefined } from "$/shared/lib/hooks.svelte";
    import ArkSheet from "$/shared/ui/ArkSheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { EventData } from "$/entities/event/model/types";

    type Props = {
        open: boolean;
        event: EventData | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    const event = useLastDefined(() => props.event);
</script>

<ArkSheet
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
</ArkSheet>
