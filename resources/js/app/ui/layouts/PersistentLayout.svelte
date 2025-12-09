<script lang="ts">
    import { useToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Toaster from "$/shared/ui/Toaster.svelte";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    useToaster();
</script>

<Toaster {toaster} />

{@render children()}
