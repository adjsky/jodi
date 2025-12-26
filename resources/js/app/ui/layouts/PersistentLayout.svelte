<script lang="ts">
    import { m } from "$/paraglide/messages";
    import { useToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import { toaster } from "$/shared/lib/toast";
    import { createActionBanner } from "$/shared/ui/ActionBanner.svelte";
    import Toaster from "$/shared/ui/Toaster.svelte";
    import { watch } from "runed";
    import { useRegisterSW } from "virtual:pwa-register/svelte";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    const { needRefresh, updateServiceWorker } = useRegisterSW();

    watch(
        () => [$needRefresh],
        () => {
            if (!$needRefresh) {
                return;
            }

            createActionBanner(m["pwa-update.title"](), {
                action: m["pwa-update.action"](),
                onAccept() {
                    void updateServiceWorker();
                },
                autoclose: false
            });
        }
    );

    useToaster();
</script>

<Toaster {toaster} />

{@render children()}
