<script lang="ts">
    import Toaster from "$/shared/ui/primitives/Toaster.svelte";
    import { toaster } from "$/shared/utils/toaster";
    import { useFlashToast } from "$/shared/utils/use-flash-toast.svelte";

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
