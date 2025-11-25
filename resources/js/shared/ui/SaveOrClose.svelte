<script lang="ts">
    import { X } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";

    import { tw } from "../lib/styles";

    import type { WithClassName } from "../lib/styles";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLButtonAttributes,
        {
            variant: "save" | "close";
        }
    >;

    const { variant, ...rest }: Props = $props();
</script>

{#if variant == "close"}
    <button
        {...rest}
        type="button"
        class={tw(
            "flex size-6 items-center justify-center rounded-full bg-cream-100",
            rest.class
        )}
    >
        <X class="text-lg text-cream-700" />
    </button>
{:else}
    <button
        {...rest}
        class={tw("leading-[1.5rem] font-bold text-brand", rest.class)}
    >
        {m["common.save"]()}
    </button>
{/if}
