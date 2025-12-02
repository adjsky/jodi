<script lang="ts">
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        title?: string;
    };

    const { title, children, ...props }: Props = $props();
</script>

<section {...props}>
    {#if title}
        <h2 class="text-lg font-bold">{title}</h2>
    {/if}
    <div class="mt-3 rounded-xl border border-cream-950 bg-white px-3">
        {@render children?.()}
    </div>
</section>
