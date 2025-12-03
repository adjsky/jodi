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
        "flex h-15.5 w-full items-center justify-center rounded-xl font-bold disabled:cursor-not-allowed disabled:opacity-50",
        variant == "main" && "border border-cream-950 bg-brand text-white",
        variant == "secondary" &&
            "border border-cream-950 bg-cream-50 text-cream-700",
        rest.class
    )}
>
    {@render children?.()}
</button>
