<script lang="ts">
    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
        onConfirm?: VoidFunction;
        onAbort?: VoidFunction;
    };

    const { children, onConfirm, onAbort }: Props = $props();
</script>
