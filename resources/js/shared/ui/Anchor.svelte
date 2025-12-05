<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { tw } from "$/shared/lib/styles";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { HTMLAnchorAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLAnchorAttributes,
        {
            variant?: "main" | "secondary";
            href: string;
        }
    >;

    const { children, variant = "main", ...rest }: Props = $props();
</script>

<Link
    {...rest}
    class={tw(
        "flex h-15 w-full items-center justify-center rounded-xl font-bold",
        variant == "main" && "bg-brand text-white outline outline-cream-950",
        variant == "secondary" &&
            "bg-cream-50 text-cream-700 outline outline-cream-950",
        rest.class
    )}
>
    {@render children?.()}
</Link>
