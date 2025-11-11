<script lang="ts">
    import Logo from "$/shared/assets/logo.svg";
    import { tw } from "$/shared/lib/styles";

    import type { Snippet } from "svelte";

    type Props = {
        icon: Snippet;
        title: string;
        description?: string;
        class?: string;
    };

    const { icon, title, description, ...props }: Props = $props();
</script>

<div {...props} class={tw("mx-auto w-max space-y-2.5", props.class)}>
    <div class="flex items-center justify-center gap-3">
        <img src={Logo} width={120} height={55} alt="" />
        {@render icon()}
    </div>
    <h1 class="text-center text-2xl font-bold">{title}</h1>
    {#if description}
        <p>{description}</p>
    {/if}
</div>
