<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { ChevronLeft } from "@lucide/svelte";

    import type { UrlMethodPair } from "@inertiajs/core";
    import type { Snippet } from "svelte";

    type Props = {
        back: string | UrlMethodPair;
        title?: string;
        viewTransition?: boolean;
        children: Snippet;
        action?: Snippet;
    };

    const { back, title, viewTransition, children, action }: Props = $props();
</script>

<div class="fixed inset-0 z-10 flex flex-col bg-cream-50 px-4 py-3">
    <div class="relative flex items-center">
        <Link href={back} {viewTransition} class="p-2">
            <ChevronLeft class="text-4xl" />
        </Link>
        {#if title}
            <span
                class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
            >
                {title}
            </span>
        {/if}
        {@render action?.()}
    </div>
    {@render children()}
</div>
