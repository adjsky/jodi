<script lang="ts">
    import { tw } from "$/shared/lib/styles/tw";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"];

    const { children, ...props }: Props = $props();
</script>

<div
    {...props}
    class={tw("flex min-h-0 grow flex-col px-safe-offset-4", props.class)}
>
    {@render children?.()}
</div>
