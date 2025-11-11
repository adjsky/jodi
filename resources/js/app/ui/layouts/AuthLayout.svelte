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

<main class="px-4" id="auth-layout">
    {@render children()}
</main>

<style>
    #auth-layout::after {
        content: "";
        position: absolute;

        bottom: 0;
        left: 0;

        width: 100%;
        height: 170px;

        background: var(--color-yellow);

        clip-path: ellipse(75% 100% at 50% 100%);
        pointer-events: none;
        z-index: -1;
    }
</style>
