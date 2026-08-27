<script lang="ts">
    import { ChevronLeft } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles/tw";

    import { context } from "../model/context";

    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["div"], "title" | "children"> & {
        shape?: "flat" | "arched";
        title?: string | Snippet;
        action?: Snippet;
    };

    const { shape = "arched", title, action, ...props }: Props = $props();

    const ctx = context.getOr(null);
</script>

<div
    {...props}
    class={tw(
        "relative flex shrink-0 items-center justify-between px-safe-offset-4",
        props.class
    )}
    data-shape={shape}
>
    <button
        class="-ms-2 p-2"
        onclick={() => {
            if (!ctx) return;
            ctx.open = false;
        }}
    >
        <ChevronLeft class="text-4xl" />
    </button>
    {#if title}
        {#if typeof title == "function"}
            {@render title()}
        {:else}
            <span class="absolute left-1/2 -translate-x-1/2 text-xl font-bold">
                {title}
            </span>
        {/if}
    {/if}
    {@render action?.()}
</div>

<style>
    [data-shape="arched"]::before,
    [data-shape="arched"]::after {
        position: absolute;
        top: 100%;
        width: 2.5rem;
        height: 3.5rem;
        content: "";
        pointer-events: none;
    }

    [data-shape="arched"]::before {
        left: 0;
        background: radial-gradient(
            ellipse 2.5rem 3.5rem at bottom right,
            transparent calc(100% - 1px),
            var(--color-cream-50) 100%
        );
    }

    [data-shape="arched"]::after {
        right: 0;
        background: radial-gradient(
            ellipse 2.5rem 3.5rem at bottom left,
            transparent calc(100% - 1px),
            var(--color-cream-50) 100%
        );
    }
</style>
