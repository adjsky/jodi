<script lang="ts">
    import { tw } from "#shared/lib/styles/tw.ts";

    import { use_loading_debounce } from "../lib/svelte/use-loading-debounce.svelte";
    import Loader from "./Loader.svelte";

    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = Exclude<HTMLButtonAttributes, "type"> & {
        variant?: "main" | "secondary" | "inline";
        type: NonNullable<HTMLButtonAttributes["type"]>;
        loading?: boolean;
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
    };

    const {
        variant = "main",
        loading = false,
        delay = 200,
        disabled,
        children,
        ...rest
    }: Props = $props();

    const is_loader_visible = use_loading_debounce(
        () => loading,
        () => delay
    );
</script>

<button
    {...rest}
    disabled={is_loader_visible.current || disabled}
    class={tw(
        "font-bold disabled:cursor-not-allowed",
        variant !== "inline" && "flex h-15 w-full items-center justify-center rounded-xl",
        variant === "main" && "bg-brand text-white outline outline-cream-950",
        variant === "secondary" && "bg-cream-50 text-cream-700 outline outline-cream-950",
        variant === "inline" && "inline-flex text-brand",
        rest.class
    )}
    aria-busy={is_loader_visible.current}
>
    {#if is_loader_visible.current}
        <Loader />
    {:else}
        {@render children?.()}
    {/if}
</button>
