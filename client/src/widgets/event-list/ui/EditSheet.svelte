<script lang="ts">
    import { LastMatching } from "$/shared/lib/svelte/last-matching.svelte";
    import Sheet from "$/shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { EventData } from "$/entities/event";

    type Props = {
        open: boolean;
        event: EventData | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    const event = new LastMatching(
        () => props.event,
        (event) => event !== null
    );
</script>

<Sheet
    bind:open
    max_height={0.9}
    snap_points={[0.6, 1]}
    default_snap_point={0.6}
    on_exit_complete={() => {
        event.reset();
    }}
>
    {#if event.current}
        <EditForm event={event.current} on_close={() => (open = false)} />
    {/if}
</Sheet>
