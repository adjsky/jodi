<script lang="ts">
    import ActionButton from "./ActionButton.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        title: string;
        onclick: VoidFunction;
        children: Snippet;
    };

    const { title, onclick, children }: Props = $props();
</script>

<div class="flex items-center gap-2">
    <span class="text-lg text-cream-50">{title}</span>
    <ActionButton {onclick}>{@render children()}</ActionButton>
</div>
