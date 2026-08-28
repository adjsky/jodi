<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles/tw";

    import InfoActionRow from "./InfoActionRow.svelte";

    import type { UrlMethodPair, VisitOptions } from "@inertiajs/core";
    import type { ClassName } from "$/shared/lib/styles/tw";
    import type { Snippet } from "svelte";

    type Props = VisitOptions & {
        href: string | UrlMethodPair;
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
    class={tw(
        "group data-selected:font-bold",
        selected && "font-bold",
        props.class
    )}
    showProgress={false}
    replace
    preserveUrl
    preserveState
    preserveScroll
/>
