<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles";
    import { boolAttr } from "runed";

    import InfoActionRow from "./InfoActionRow.svelte";

    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        icon?: Snippet;
        selected?: boolean;
    };

    const { icon, selected, children, ...props }: Props = $props();
</script>

{#snippet endIcon()}
    <Check class="text-xl not-group-data-selected:hidden" />
{/snippet}

<InfoActionRow
    {...props}
    {children}
    {endIcon}
    startIcon={icon}
    data-selected={boolAttr(selected)}
    class={tw("group data-selected:font-bold", props.class)}
/>
