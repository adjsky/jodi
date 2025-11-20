<script lang="ts">
    import type { SvelteHTMLElements } from "svelte/elements";

    const props: SvelteHTMLElements["input"] = $props();
</script>

<input
    {...props}
    class="mt-5 form-input w-full border-none bg-transparent p-0 text-xl font-bold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    placeholder="Задача"
/>
