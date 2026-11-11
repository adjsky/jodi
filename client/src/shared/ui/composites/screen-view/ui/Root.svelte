<script lang="ts">
    import { tw } from "#shared/lib/styles/tw.ts";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"];

    const id = $props.id();
    const { children, ...props }: Props = $props();
</script>

<div
    {...props}
    id="screen-view:{id}:root"
    class={tw(
        "flex h-full flex-col overflow-hidden bg-cream-50 pt-3 pb-keyboard-offset-5",
        props.class
    )}
>
    {@render children?.()}
</div>
