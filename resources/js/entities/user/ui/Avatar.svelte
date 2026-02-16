<script lang="ts" generics="T extends keyof SvelteHTMLElements = 'button'">
    import { tw } from "$/shared/lib/styles";
    import ExclamationMark from "$/shared/ui/Warning.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props<T extends keyof SvelteHTMLElements> = SvelteHTMLElements[T] & {
        as?: T;
        name: string;
        warn?: boolean;
    };

    const { as, name, warn, ...props }: Props<T> = $props();
</script>

<svelte:element
    this={as ?? "button"}
    {...props}
    class={tw(
        "relative flex size-9 items-center justify-center rounded-full bg-brand text-lg font-semibold text-white outline outline-cream-950",
        props.class
    )}
>
    {name[0].toLocaleUpperCase()}
    {#if warn}
        <ExclamationMark
            class="absolute -right-1 -bottom-1 z-1 text-xl text-cream-950"
        />
    {/if}
</svelte:element>
