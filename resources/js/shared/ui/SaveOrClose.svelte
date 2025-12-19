<script lang="ts">
    import { Check, X } from "@lucide/svelte";

    import { tw } from "../lib/styles";

    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        variant: "save" | "close";
    };

    const { variant, ...rest }: Props = $props();
</script>

<button
    {...rest}
    type={variant == "save" ? "submit" : "button"}
    class={tw(
        "flex size-7 shrink-0 items-center justify-center rounded-full",
        variant == "close" && "bg-cream-100",
        variant == "save" && "bg-brand",
        rest.class
    )}
>
    {#if variant == "close"}
        <X class="text-xl text-cream-700" />
    {:else}
        <Check class="text-xl text-white" />
    {/if}
</button>
