<script lang="ts">
    import { tw } from "$/shared/lib/styles";
    import { useLoadingDebounce } from "$/shared/lib/use-loading-debounce.svelte";

    import type { WithClassName } from "$/shared/lib/styles";
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
        "flex h-15 w-full items-center justify-center rounded-xl font-bold disabled:cursor-not-allowed disabled:opacity-50",
        variant == "main" && "border border-cream-950 bg-brand text-white",
        variant == "secondary" && "border border-cream-950 text-cream-600",
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
