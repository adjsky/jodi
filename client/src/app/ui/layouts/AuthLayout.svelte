<script lang="ts">
    import type { Snippet } from "svelte";

    type Props = {
        children: Snippet;
    };

    const { children }: Props = $props();
</script>

<main class="mt-[18.5vh] px-4" id="auth-layout">
    {@render children()}
</main>

<style>
    #auth-layout::after {
        content: "";
        position: fixed;

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
