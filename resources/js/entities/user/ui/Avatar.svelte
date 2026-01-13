<script lang="ts" generics="T extends keyof SvelteHTMLElements = 'button'">
    import { tw } from "$/shared/lib/styles";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props<T extends keyof SvelteHTMLElements> = SvelteHTMLElements[T] & {
        as?: T;
        name: string;
    };

    const { as, name, ...props }: Props<T> = $props();
</script>

<svelte:element
    this={as ?? "button"}
    {...props}
    class={tw(
        "flex size-9 items-center justify-center rounded-full bg-brand text-lg font-semibold text-white outline outline-cream-950",
        props.class
    )}
>
    {name[0].toLocaleUpperCase()}
</svelte:element>
