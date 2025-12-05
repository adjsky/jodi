<script lang="ts">
    import { ChevronLeft } from "@lucide/svelte";

    import { tw } from "../lib/styles";

    import type { WithClassName } from "../lib/styles";
    import type { HTMLButtonAttributes } from "svelte/elements";

    const props: WithClassName<HTMLButtonAttributes> = $props();
</script>

<button {...props} class={tw("p-2", props.class)}>
    <ChevronLeft class="text-4xl" />
</button>
