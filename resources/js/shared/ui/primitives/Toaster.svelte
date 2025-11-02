<script lang="ts">
    import { Toast, Toaster } from "@ark-ui/svelte/toast";
    import { CircleAlert, Info } from "@lucide/svelte";

    import type { CreateToasterReturn } from "@ark-ui/svelte/toast";

    type Props = {
        toaster: CreateToasterReturn;
    };

    const { toaster }: Props = $props();
</script>

<Toaster {toaster}>
    {#snippet children(toast)}
        <Toast.Root
            class="flex w-full max-w-89 items-center gap-2 rounded-lt bg-cream-800 px-2.5 py-2"
        >
            <div class="shrink-0 text-3xl">
                {#if toast().type == "info"}
                    <Info />
                {:else if toast().type == "error"}
                    <CircleAlert class="text-red" />
                {/if}
            </div>
            <Toast.Title class="text-sm font-semibold text-white">
                {toast().title}
            </Toast.Title>
        </Toast.Root>
    {/snippet}
</Toaster>

<style>
    :global([data-scope="toast"][data-part="root"]) {
        translate: var(--x) var(--y);
        scale: var(--scale);
        z-index: var(--z-index);
        height: var(--height);
        opacity: var(--opacity);
        will-change: translate, opacity, scale;
        transition:
            translate 400ms,
            scale 400ms,
            opacity 400ms,
            height 400ms,
            box-shadow 200ms;
        transition-timing-function: cubic-bezier(0.21, 1.02, 0.73, 1);

        &[data-state="closed"] {
            transition:
                translate 400ms,
                scale 400ms,
                opacity 200ms;
            transition-timing-function: cubic-bezier(0.06, 0.71, 0.55, 1);
        }
    }
</style>
