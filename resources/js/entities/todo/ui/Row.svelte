<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"] & {
        checkbox: Snippet;
        edit: Snippet;
        grip: Snippet;
    };

    const { checkbox, edit, grip, ...props }: Props = $props();
</script>

<div
    {...props}
    class={tw("grid grid-cols-[auto_1fr_auto] items-center gap-2", props.class)}
>
    {@render checkbox()}
    {@render edit()}
    {@render grip()}
</div>
