<script lang="ts">
    import { Check } from "@lucide/svelte";

    import { tw } from "../lib/styles";

    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        label: string | Snippet;
        checked?: boolean;
    };

    let { label, checked = $bindable(false), ...props }: Props = $props();
</script>

<button
    type="button"
    onclick={() => {
        checked = !checked;
    }}
    {...props}
    class={tw("flex w-full items-center gap-2 py-2.5 font-medium", props.class)}
>
    <span
        class={[
            "flex size-5.5 items-center justify-center rounded-full border border-cream-950",
            checked && "bg-cream-950"
        ]}
    >
        {#if checked}
            <Check class="shrink-0 text-md text-white" />
        {/if}
    </span>
    {#if typeof label == "function"}
        {@render label?.()}
    {:else}
        <span>{label}</span>
    {/if}
</button>
