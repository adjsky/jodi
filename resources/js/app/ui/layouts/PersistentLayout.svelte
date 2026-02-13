<script lang="ts">
    import { useFlashToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { useServiceWorker } from "$/shared/lib/service-worker";
    import { onMount } from "svelte";
    import { Toaster } from "svelte-sonner";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    onMount(() => {
        void PushSubscription.synchronize();
        const cleanup = PushSubscription.setupListeners();
        return () => cleanup.then((c) => c());
    });

    useServiceWorker();
    useFlashToaster();
</script>

<Toaster />

{@render children()}
