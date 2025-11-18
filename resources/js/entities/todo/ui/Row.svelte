<script lang="ts">
    import type { Snippet } from "svelte";

    type Props = {
        checkbox: Snippet;
        edit: Snippet;
        grip: Snippet;
    };

    const { checkbox, edit, grip }: Props = $props();
</script>

<div class="flex items-center gap-2">
    {@render checkbox()}
    {@render edit()}
    {@render grip()}
</div>
