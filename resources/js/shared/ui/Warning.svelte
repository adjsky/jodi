<script lang="ts">
    import { tw } from "../lib/styles";

    import type { SVGAttributes } from "svelte/elements";

    const props: SVGAttributes<SVGElement> = $props();
</script>

<svg
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    {...props}
    class={tw(
        "lucide rounded-full border border-cream-950 bg-yellow",
        props.class
    )}
>
    <line x1="12" x2="12" y1="8" y2="12" />
    <line x1="12" x2="12.01" y1="16" y2="16" />
</svg>
