<script lang="ts">
    import { useHandleRouterException } from "$/shared/inertia/use-handle-router-exception.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Toaster from "$/shared/ui/Toaster.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    const { children, ...props }: SvelteHTMLElements["main"] = $props();

    useHandleRouterException();
</script>

<Toaster {toaster} />

<main {...props}>
    {@render children?.()}
</main>
