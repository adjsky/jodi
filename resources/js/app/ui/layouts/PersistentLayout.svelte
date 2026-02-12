<script lang="ts">
    import { progress } from "@inertiajs/svelte";
    import { m } from "$/paraglide/messages";
    import { useFlashToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { createActionBanner } from "$/shared/ui/ActionBanner.svelte";
    import { watch } from "runed";
    import { onMount } from "svelte";
    import { Toaster } from "svelte-sonner";
    import { useRegisterSW } from "virtual:pwa-register/svelte";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    const { needRefresh, updateServiceWorker } = useRegisterSW();

    onMount(() => {
        void PushSubscription.synchronize();
    });

    watch(
        () => [$needRefresh],
        () => {
            if (!$needRefresh) {
                return;
            }

            createActionBanner(m["pwa-update.title"](), {
                action: m["pwa-update.action"](),
                onAccept() {
                    progress.reveal(true);
                    progress.start();
                    void updateServiceWorker();
                },
                autoclose: false
            });
        }
    );

    useFlashToaster();
</script>

<Toaster />

{@render children()}
