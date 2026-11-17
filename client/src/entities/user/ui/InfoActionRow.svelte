<script lang="ts">
    import { tw } from "#shared/lib/styles/tw.ts";
    import { use_loading_debounce } from "#shared/lib/svelte/use-loading-debounce.svelte.ts";
    import Loader from "#shared/ui/Loader.svelte";

    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        start_icon?: Snippet;
        end_icon?: Snippet;
        children?: Snippet;
        is_loading?: boolean;
    };

    const { start_icon, end_icon, children, is_loading = false, ...props }: Props = $props();

    const is_loader_visible = use_loading_debounce(() => is_loading);
</script>

<button
    type="button"
    aria-busy={is_loading}
    disabled={is_loading}
    {...props}
    class={tw(
        "flex h-14 w-full items-center justify-between border-cream-300 text-lg font-medium not-last:border-b disabled:cursor-not-allowed",
        props.class
    )}
>
    {#if is_loader_visible.current}
        <Loader />
    {:else}
        <span class="flex items-center gap-2">
            {@render start_icon?.()}
            {@render children?.()}
        </span>
        {@render end_icon?.()}
    {/if}
</button>
