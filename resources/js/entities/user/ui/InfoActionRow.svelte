<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLButtonAttributes,
        {
            icon?: Snippet;
        }
    >;

    const { icon, children, ...props }: Props = $props();
</script>

<button
    {...props}
    class={tw(
        "flex w-full border-cream-300 py-3 text-lg font-medium not-last:border-b",
        props.class
    )}
>
    {@render icon?.()}
    {@render children?.()}
</button>
