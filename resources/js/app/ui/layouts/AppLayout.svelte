<script lang="ts">
    import { useFlashToast } from "$/shared/inertia/use-flash-toast.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Toaster from "$/shared/ui/Toaster.svelte";

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
