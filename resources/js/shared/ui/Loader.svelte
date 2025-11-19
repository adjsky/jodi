<script lang="ts">
    import { tw } from "../lib/styles";

    import type { WithClassName } from "../lib/styles";
    import type { SvelteHTMLElements } from "svelte/elements";

    const props: WithClassName<SvelteHTMLElements["div"]> = $props();
</script>

<div {...props} class={tw("flex gap-1 *:h-6 *:w-1 *:bg-current", props.class)}>
    <span class="animate-pulse [animation-duration:0.6s]"></span>
    <span
        class="animate-pulse [animation-delay:0.2s] [animation-duration:0.6s]"
    ></span>
    <span
        class="animate-pulse [animation-delay:0.4s] [animation-duration:0.6s]"
    ></span>
</div>
