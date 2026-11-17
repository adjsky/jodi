<script lang="ts">
    import { parseAbsolute, toCalendarDate } from "@internationalized/date";
    import { Trash } from "@lucide/svelte";
    import { Recurrence } from "#entities/recurrence/mod.ts";
    import { TIMEZONE } from "#shared/cfg/constants.ts";
    import ToolbarAction from "#shared/ui/ToolbarAction.svelte";

    import type { RecurrenceScope } from "#entities/recurrence/mod.ts";

    type Props = {
        title: {
            recurring: string;
            general: string;
        };
        tooltip: string;
        recurring: boolean;
        scopeLabels: { this: string; following: string; all: string };
        onDelete?: (scope: RecurrenceScope) => void;
    };

    const { title, tooltip, recurring, scopeLabels, onDelete }: Props =
        $props();

    let open = $state(false);
</script>

<Recurrence.ScopeDialog
    bind:open
    {scopeLabels}
    title={recurring ? title.recurring : title.general}
    fallback={!recurring}
    onConfirm={(scope) => onDelete?.(scope)}
>
    {#snippet trigger(props)}
        <ToolbarAction {...props()} {tooltip}>
            <Trash />
        </ToolbarAction>
    {/snippet}
</Recurrence.ScopeDialog>
