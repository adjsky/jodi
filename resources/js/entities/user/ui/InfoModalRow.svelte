<script lang="ts">
    import { ChevronRight } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLButtonAttributes,
        {
            title: string;
            icon?: Snippet;
        }
    >;

    const { title, children, icon, ...props }: Props = $props();
</script>

<button
    {...props}
    class={tw(
        "flex w-full justify-between border-cream-300 py-3 text-lg font-medium not-last:border-b",
        props.class
    )}
>
    <span>{@render icon?.()} {title}</span>
    <span class="flex items-center gap-2 text-cream-500">
        {@render children?.()}
        <ChevronRight class="text-xl" />
    </span>
</button>
