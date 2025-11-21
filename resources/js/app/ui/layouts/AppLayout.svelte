<script lang="ts">
    import { toaster } from "$/shared/lib/toast";
    import Toaster from "$/shared/ui/Toaster.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();
</script>

<Toaster {toaster} />

<main>
    {@render children()}
</main>
