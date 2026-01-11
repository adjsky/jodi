<script lang="ts">
    import { Portal } from "@ark-ui/svelte";
    import { Link } from "@inertiajs/svelte";
    import { ChevronLeft } from "@lucide/svelte";

    import { tw } from "../lib/styles";

    import type { UrlMethodPair } from "@inertiajs/core";
    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["div"], "title"> & {
        back: string | UrlMethodPair | Snippet;
        title?: string | Snippet;
        viewTransition?: boolean;
        children: Snippet;
        action?: Snippet;
    };

    const id = $props.id();
    const { back, title, viewTransition, children, action, ...props }: Props =
        $props();
</script>

<Portal>
    <div
        id="floating-view-{id}"
        {...props}
        class={tw(
            "fixed inset-0 z-100 flex h-full flex-col overflow-y-scroll bg-cream-50 px-4 py-3",
            props.class
        )}
    >
        <div class="relative flex items-center justify-between">
            {#if typeof back == "function"}
                {@render back()}
            {:else}
                <Link href={back} {viewTransition} class="p-2">
                    <ChevronLeft class="text-4xl" />
                </Link>
            {/if}
            {#if title}
                {#if typeof title == "function"}
                    {@render title()}
                {:else}
                    <span
                        class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
                    >
                        {title}
                    </span>
                {/if}
            {/if}
            {@render action?.()}
        </div>
        {@render children()}
    </div>
</Portal>
