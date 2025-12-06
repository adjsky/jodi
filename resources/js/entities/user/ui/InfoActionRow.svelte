<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        startIcon?: Snippet;
        endIcon?: Snippet;
    };

    const { startIcon, endIcon, children, ...props }: Props = $props();
</script>

<button
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
</button>
