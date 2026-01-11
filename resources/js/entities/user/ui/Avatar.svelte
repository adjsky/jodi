<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["button"] & { name: string };

    const { name, ...props }: Props = $props();
</script>

<button
    {...props}
    class={tw(
        "flex size-9 items-center justify-center rounded-full bg-brand text-lg font-semibold text-white outline outline-cream-950",
        props.class
    )}
>
    {name[0].toLocaleUpperCase()}
</button>
