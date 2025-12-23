<script lang="ts">
    import { m } from "$/paraglide/messages";
    import { useToaster } from "$/shared/inertia/use-flash-toaster.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Button from "$/shared/ui/Button.svelte";
    import Toaster from "$/shared/ui/Toaster.svelte";
    import { useRegisterSW } from "virtual:pwa-register/svelte";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    const { needRefresh, updateServiceWorker } = useRegisterSW();

    useToaster();
</script>

<Toaster {toaster} />

{#if $needRefresh}
    <div
        class="flex items-center justify-between rounded-b-xl border border-cream-950 bg-peach px-4 py-2"
    >
        <h3 class="text-ms font-semibold">{m["pwa-update.title"]()}</h3>
        <Button
            class="h-auto w-max px-2 py-1 text-ms"
            onclick={() => updateServiceWorker()}
        >
            {m["pwa-update.action"]()}
        </Button>
    </div>
{/if}

{@render children()}
