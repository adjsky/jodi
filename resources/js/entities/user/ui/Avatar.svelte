<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = WithClassName<SvelteHTMLElements["div"], { name: string }>;

    const { name, ...props }: Props = $props();
</script>

<div
    {...props}
    class={tw(
        "flex size-9 items-center justify-center rounded-full bg-brand text-lg font-semibold text-white outline outline-cream-950",
        props.class
    )}
>
    {name[0].toLocaleUpperCase()}
</div>
