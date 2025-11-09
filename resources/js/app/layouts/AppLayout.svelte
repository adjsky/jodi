<script lang="ts">
    import Toaster from "$/shared/ui/primitives/Toaster.svelte";
    import { toaster } from "$/shared/utils/toaster";
    import { useFlashToast } from "$/shared/utils/use-flash-toast.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();

    useFlashToast();
</script>

<Toaster {toaster} />

<main>
    {@render children()}
</main>
