<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles";
    import { boolAttr } from "runed";

    import InfoActionRow from "./InfoActionRow.svelte";

    import type {
        RequestPayload,
        UrlMethodPair,
        VisitCallbacks
    } from "@inertiajs/core";
    import type { ClassName } from "$/shared/lib/styles";
    import type { Snippet } from "svelte";

    type Props = Partial<VisitCallbacks> & {
        href: UrlMethodPair;
        data?: RequestPayload;
        icon?: Snippet;
        children?: Snippet;
        class?: ClassName;
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
    replace
/>
