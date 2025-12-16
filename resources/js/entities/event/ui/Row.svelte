<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["button"], "title"> & {
        time: Snippet;
        title: Snippet;
    };

    const { time, title, ...props }: Props = $props();
</script>

<button
    {...props}
    class={tw("flex items-center gap-3 py-2.25 text-lg", props.class)}
>
    {@render time()}
    {@render title()}
</button>
