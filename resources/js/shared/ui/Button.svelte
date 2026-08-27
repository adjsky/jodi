<script lang="ts">
    import { tw } from "$/shared/lib/styles/tw";

    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = Exclude<HTMLButtonAttributes, "type"> & {
        variant?: "main" | "secondary" | "inline";
        type: NonNullable<HTMLButtonAttributes["type"]>;
    };

    const { children, variant = "main", ...rest }: Props = $props();
</script>

<button
    {...rest}
    class={tw(
        "font-bold disabled:cursor-not-allowed",
        variant != "inline" &&
            "flex h-15 w-full items-center justify-center rounded-xl",
        variant == "main" && "bg-brand text-white outline outline-cream-950",
        variant == "secondary" &&
            "bg-cream-50 text-cream-700 outline outline-cream-950",
        variant == "inline" && "inline-flex text-brand",
        rest.class
    )}
>
    {@render children?.()}
</button>
