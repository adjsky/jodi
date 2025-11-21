<script lang="ts">
    import { CircleX } from "@lucide/svelte";

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
    <button {...rest} type="button" class={tw("", rest.class)}>
        <CircleX class="text-xl" />
    </button>
{:else}
    <button {...rest} class={tw("font-bold text-brand", rest.class)}>
        Сохранить
    </button>
{/if}
