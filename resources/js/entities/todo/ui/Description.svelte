<script lang="ts">
    import { Field } from "@ark-ui/svelte/field";
    import { m } from "$/paraglide/messages";

    import type { SvelteHTMLElements } from "svelte/elements";

    const props: SvelteHTMLElements["textarea"] = $props();
</script>

<Field.Textarea
    {...props}
    autoresize
    class="mt-6 form-input field-sizing-content w-full border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    placeholder={m["todos.placeholders.description"]()}
/>
