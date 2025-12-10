<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { ChevronRight } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles";

    import type { RequestPayload, UrlMethodPair } from "@inertiajs/core";
    import type { ClassName } from "$/shared/lib/styles";
    import type { Snippet } from "svelte";

    type Props = {
        href: UrlMethodPair;
        data?: RequestPayload;
        class?: ClassName;
        title: string;
        icon?: Snippet;
        children?: Snippet;
    };

    const { title, children, icon, ...props }: Props = $props();
</script>

<Link
    {...props}
    class={tw(
        "flex w-full items-center justify-between border-cream-300 py-3.5 text-lg font-medium not-last:border-b",
        props.class
    )}
    viewTransition
>
    <span class="flex items-center gap-2">{@render icon?.()} {title}</span>
    <span class="flex items-center gap-2 text-cream-500">
        {@render children?.()}
        <ChevronRight class="text-xl" />
    </span>
</Link>
