<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["button"], "title"> & {
        time: Snippet;
        title: Snippet;
        color: string | null;
    };

    const { time, title, color, ...props }: Props = $props();
</script>

<button
    {...props}
    class={tw(
        "flex items-center gap-2 rounded-xl border border-cream-200 px-2 py-2.25 text-lg",
        !color && "bg-brand/10",
        props.class
    )}
    style={color ? `background: ${color}1a;` : null}
>
    {@render time()}
    {@render title()}
</button>
