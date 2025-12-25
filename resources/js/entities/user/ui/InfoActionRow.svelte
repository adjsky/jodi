<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { tw } from "$/shared/lib/styles";

    import type {
        RequestPayload,
        UrlMethodPair,
        VisitCallbacks
    } from "@inertiajs/core";
    import type { ClassName } from "$/shared/lib/styles";
    import type { Snippet } from "svelte";

    type Props = Partial<VisitCallbacks> & {
        startIcon?: Snippet;
        endIcon?: Snippet;
        href: UrlMethodPair;
        data?: RequestPayload;
        class?: ClassName;
        children?: Snippet;
        [x: string]: unknown;
    };

    const { startIcon, endIcon, children, ...props }: Props = $props();
</script>

<Link
    {...props}
    class={tw(
        "flex w-full items-center justify-between border-cream-300 py-3.5 text-lg font-medium not-last:border-b",
        props.class
    )}
    viewTransition
>
    <span class="flex items-center gap-2">
        {@render startIcon?.()}
        {@render children?.()}
    </span>
    {@render endIcon?.()}
</Link>
