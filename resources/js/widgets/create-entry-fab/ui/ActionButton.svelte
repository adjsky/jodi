<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { HTMLButtonAttributes } from "svelte/elements";

    const { children, ...props }: HTMLButtonAttributes = $props();
</script>

<button
    {...props}
    class={tw(
        "flex size-15 items-center justify-center rounded-full bg-brand text-3xl text-white",
        props.class
    )}
>
    {@render children?.()}
</button>
