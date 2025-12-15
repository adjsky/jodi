<script lang="ts">
    import { m } from "$/paraglide/messages";
    import { tw } from "$/shared/lib/styles";

    import type { SvelteHTMLElements } from "svelte/elements";

    const props: SvelteHTMLElements["input"] = $props();
</script>

<input
    {...props}
    class={tw(
        "form-input w-full border-none bg-transparent p-0 text-xl font-bold text-cream-950 placeholder:text-cream-600 focus:ring-0",
        props.class
    )}
    placeholder={m["events.placeholders.title"]()}
/>
