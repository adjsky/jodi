<script lang="ts">
    import { m } from "#paraglide/messages.js";
    import SadCat from "#shared/assets/sad-cat.svg";

    import Button from "./Button.svelte";

    type Props = {
        message: string;
        onRetry?: VoidFunction;
    };

    const { message, onRetry }: Props = $props();
</script>

<img
    src={SadCat}
    width={82}
    height={85}
    alt=""
    decoding="async"
    class="mx-auto w-full max-w-28"
/>

<p class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium">
    {message}
</p>

{#if onRetry}
    <Button
        class="mx-auto mt-4"
        type="button"
        variant="inline"
        onclick={onRetry}
    >
        {m["common.try-again"]()}
    </Button>
{/if}
