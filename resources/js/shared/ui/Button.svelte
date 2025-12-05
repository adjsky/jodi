<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLButtonAttributes,
        {
            variant?: "main" | "secondary";
        }
    >;

    const { children, variant = "main", ...rest }: Props = $props();
</script>

<button
    {...rest}
    class={tw(
        "flex h-15 w-full items-center justify-center rounded-xl font-bold disabled:cursor-not-allowed",
        variant == "main" && "bg-brand text-white outline outline-cream-950",
        variant == "secondary" &&
            "bg-cream-50 text-cream-700 outline outline-cream-950",
        rest.class
    )}
>
    {@render children?.()}
</button>
