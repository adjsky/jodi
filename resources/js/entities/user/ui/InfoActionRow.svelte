<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { tw } from "$/shared/lib/styles";

    import type { ComponentProps, Snippet } from "svelte";

    type Props = ComponentProps<Link> & {
        startIcon?: Snippet;
        endIcon?: Snippet;
    };

    const { startIcon, endIcon, children, ...props }: Props = $props();
</script>

<Link
    {...props}
    class={tw(
        "flex w-full items-center justify-between border-cream-300 py-3.5 text-lg font-medium not-last:border-b",
        props.class
    )}
>
    <span class="flex items-center gap-2">
        {@render startIcon?.()}
        {@render children?.()}
    </span>
    {@render endIcon?.()}
</Link>
