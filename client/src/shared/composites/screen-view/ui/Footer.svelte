<script lang="ts">
    import { tw } from "#shared/lib/styles/tw.ts";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"];

    const { children, ...props }: Props = $props();
</script>

<div {...props} class={tw("shrink-0 px-safe-offset-4", props.class)}>
    {@render children?.()}
</div>
