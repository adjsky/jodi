<script lang="ts">
    import { tw } from "$/shared/lib/css/tw";

    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<SvelteHTMLElements["button"], "title"> & {
        time: Snippet;
        title: Snippet;
        color: string | null;
    };

    const { time, title, color: providedColor, ...props }: Props = $props();

    const color = $derived(providedColor || "var(--color-tangerine)");
</script>

<button
    {...props}
    class={tw(
        "grid w-full grid-cols-[auto_1fr] items-center gap-2 rounded-md border-l-6 px-2 py-2.75 text-lg text-cream-950",
        props.class
    )}
    style:background-color="color-mix(in srgb, {color} 15%, transparent)"
    style:border-color={color}
>
    {@render time()}
    {@render title()}
</button>
