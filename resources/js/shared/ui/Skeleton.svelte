<!-- Ref: https://github.com/dvtng/react-loading-skeleton -->

<script lang="ts">
    import { tw } from "../lib/styles";

    import type { ClassName } from "../lib/styles";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = {
        class?: ClassName;
        style?: SvelteHTMLElements["span"]["style"];

        /**
         * Adds `flex-grow: 1` to the skeleton container. Use it when your skeleton
         * doesn't grow (that is, its width is 0px) within a flex container.
         * See https://github.com/dvtng/react-loading-skeleton?tab=readme-ov-file#the-skeleton-width-is-0-when-the-parent-has-display-flex.
         * */
        grow?: boolean;

        /**
         * By default, a `<br />` is inserted after each skeleton so that each skeleton
         * gets its own line. When inline is true, no line breaks are inserted.
         */
        inline?: boolean;
    };

    const { class: className, style, grow, inline = true }: Props = $props();

    const skeletonClass: ClassName = tw(
        "relative inline-flex w-full overflow-hidden rounded-lg bg-cream-900/10 leading-none select-none motion-safe:animate-pulse",
        className
    );
</script>

<svelte:element
    this={inline ? "span" : "div"}
    aria-live="polite"
    aria-busy="true"
    class={[grow && "grow"]}
>
    <span class={skeletonClass} {style}>&zwnj;</span>

    {#if inline}
        <br />
    {/if}
</svelte:element>
