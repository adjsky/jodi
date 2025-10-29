<script lang="ts">
    import { tw } from "$/shared/utils/tw";
    import { useLoadingDebounce } from "$/shared/utils/use-loading-debounce.svelte";

    import type { WithClassName } from "$/shared/utils/tw";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLButtonAttributes,
        {
            loading?: boolean;
            variant?: "main" | "secondary";
            /**
             * Delay applying loading state. Use it if you are making a fast backend
             * request, this option will prevent jagging glitches, improving the
             * overall UX.
             *
             * If set to `0`, the loader will show up immediately.
             *
             * @default 200ms
             */
            delay?: number;
        }
    >;

    const {
        children,
        variant = "main",
        disabled,
        loading = false,
        delay = 200,
        ...rest
    }: Props = $props();

    const delayedLoading = useLoadingDebounce(() => loading, delay);
</script>

<button
    {...rest}
    class={tw(
        "flex h-15 w-full items-center justify-center rounded-xl font-semibold disabled:cursor-not-allowed disabled:opacity-50 motion-safe:transition-colors",
        variant == "main" &&
            "border border-cream-950 bg-brand text-white hover:bg-white hover:text-brand",
        variant == "secondary" &&
            "hover:bg-gray-200 border border-cream-950 text-cream-700 hover:bg-white hover:text-brand",
        rest.class
    )}
    disabled={delayedLoading.current || disabled}
>
    {#if loading && delayedLoading.current}
        Loading...
    {:else}
        {@render children?.()}
    {/if}
</button>
