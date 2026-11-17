<script lang="ts">
    import { Trash } from "@lucide/svelte";
    import { Recurrence } from "#entities/recurrence/mod.ts";
    import ToolbarAction from "#shared/ui/ToolbarAction.svelte";

    import type { RecurrenceScope } from "#entities/recurrence/mod.ts";

    type Props = {
        title: {
            recurring: string;
            general: string;
        };
        tooltip: string;
        recurring: boolean;
        scope_labels: { this: string; following: string; all: string };
        on_delete?: (scope: RecurrenceScope) => void;
    };

    const { title, tooltip, recurring, scope_labels, on_delete }: Props = $props();

    let open = $state(false);
</script>

<Recurrence.ScopeDialog
    bind:open
    {scope_labels}
    title={recurring ? title.recurring : title.general}
    fallback={!recurring}
    on_confirm={(scope) => on_delete?.(scope)}
>
    {#snippet trigger(props)}
        <ToolbarAction {...props()} {tooltip}>
            <Trash />
        </ToolbarAction>
    {/snippet}
</Recurrence.ScopeDialog>
