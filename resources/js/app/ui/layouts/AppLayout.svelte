<script lang="ts">
    import { useHandleRouterException } from "$/shared/inertia/use-handle-router-exception.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Toaster from "$/shared/ui/Toaster.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();

    useHandleRouterException();
</script>

<Toaster {toaster} />

<main>
    {@render children()}
</main>
