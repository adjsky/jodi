<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { useFlashToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { useServiceWorker } from "$/shared/lib/service-worker";
    import { initializeApp } from "firebase/app";
    import { onMount } from "svelte";
    import { Toaster } from "svelte-sonner";
    import { get } from "svelte/store";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    onMount(() => {
        initializeApp(get(page).props.config.firebase);

        void PushSubscription.synchronize();
        const cleanup = PushSubscription.setupListeners();

        return () => cleanup.then((c) => c());
    });

    useServiceWorker();
    useFlashToaster();
</script>

<Toaster />

{@render children()}
