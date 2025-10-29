<script lang="ts">
    import { tw } from "$/shared/utils/tw.ts";

    import type { WithClassName } from "$/shared/utils/tw.ts";
    import type { HTMLAnchorAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLAnchorAttributes,
        {
            variant?: "main" | "secondary";
        }
    >;

    const { children, variant = "main", ...rest }: Props = $props();
</script>

<a
    {...rest}
    class={tw(
        "flex h-15 w-full items-center justify-center rounded-xl font-semibold",
        variant == "main" && "border border-cream-950 bg-brand text-white",
        variant == "secondary" && "border border-cream-950 text-cream-700",
        rest.class
    )}
>
    {@render children?.()}
</a>
