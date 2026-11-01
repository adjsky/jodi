<script lang="ts">
    import { tw } from "#shared/lib/styles/tw.ts";
    import { useLoadingDebounce } from "#shared/lib/svelte/use-loading-debounce.svelte.ts";
    import Loader from "#shared/ui/Loader.svelte";

    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        startIcon?: Snippet;
        endIcon?: Snippet;
        children?: Snippet;
        isLoading?: boolean;
    };

    const {
        startIcon,
        endIcon,
        children,
        isLoading = false,
        ...props
    }: Props = $props();

    const isLoaderVisible = useLoadingDebounce(() => isLoading);
</script>

<button
    type="button"
    aria-busy={isLoading}
    disabled={isLoading}
    {...props}
    class={tw(
        "flex h-14 w-full items-center justify-between border-cream-300 text-lg font-medium not-last:border-b disabled:cursor-not-allowed",
        props.class
    )}
>
    {#if isLoaderVisible.current}
        <Loader />
    {:else}
        <span class="flex items-center gap-2">
            {@render startIcon?.()}
            {@render children?.()}
        </span>
        {@render endIcon?.()}
    {/if}
</button>
