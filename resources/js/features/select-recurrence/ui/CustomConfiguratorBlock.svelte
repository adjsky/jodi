<script lang="ts">
    import type { Snippet } from "svelte";

    type Props = {
        title: string;
        children: Snippet;
    };

    const { title, children }: Props = $props();
</script>

<hr class="mt-4 h-px text-cream-300" />
<h3 class="mt-4 text-sm font-semibold">
    {title}
</h3>
{@render children()}
