<script lang="ts">
    import { LastMatching } from "#shared/lib/svelte/last-matching.svelte.ts";
    import Sheet from "#shared/ui/Sheet.svelte";

    import EditForm from "./EditForm.svelte";

    import type { TodoOccurrence } from "#entities/todo/mod.ts";

    type Props = {
        open: boolean;
        todo: TodoOccurrence | null;
    };

    let { open = $bindable(), ...props }: Props = $props();

    const todo = new LastMatching(
        () => props.todo,
        (todo) => todo !== null
    );
</script>

<Sheet
    bind:open
    max_height={0.9}
    snap_points={[0.6, 1]}
    default_snap_point={0.6}
    on_exit_complete={() => {
        todo.reset();
    }}
>
    {#if todo.current}
        <EditForm todo={todo.current} on_close={() => (open = false)} />
    {/if}
</Sheet>
