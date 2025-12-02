<script lang="ts">
    import Sheet from "$/shared/ui/Sheet.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        open: boolean;
        content: Snippet;
        trigger?: Snippet;
    };

    let { open = $bindable(), content, trigger }: Props = $props();
</script>

<Sheet
    bind:open
    {trigger}
    {content}
    defaultSnapPoint={0.6}
    snapPoints={[0.6, 0.95]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
    class="!pb-6"
/>
