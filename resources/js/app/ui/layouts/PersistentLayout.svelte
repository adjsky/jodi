<script lang="ts">
    import { Device } from "@capacitor/device";
    import { page } from "@inertiajs/svelte";
    import { DEVICE_ID_COOKIE } from "$/shared/cfg/constants";
    import { useFlashToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import * as Cookie from "$/shared/lib/cookie";
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

    onMount(async () => {
        if (Cookie.get(DEVICE_ID_COOKIE) != null) return;

        const { identifier } = await Device.getId();

        Cookie.set(DEVICE_ID_COOKIE, identifier, {
            maxAge: 34560000,
            sameSite: "lax",
            secure: true
        });
    });

    useServiceWorker();
    useFlashToaster();
</script>

<Toaster />

{@render children()}
