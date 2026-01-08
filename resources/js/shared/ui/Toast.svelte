<script lang="ts">
    import { CircleAlert, CircleCheck, Info } from "@lucide/svelte";

    type Props = {
        type: "info" | "error" | "success";
        title: string;
    };

    const { type, title }: Props = $props();
</script>

<div class="flex w-full items-center gap-2 rounded-lt bg-cream-800 px-2.5 py-2">
    <div class="shrink-0 text-3xl">
        {#if type == "info"}
            <Info class="text-yellow" />
        {:else if type == "error"}
            <CircleAlert class="text-red" />
        {:else if type == "success"}
            <CircleCheck class="text-green" />
        {/if}
    </div>
    <p class="font-semibold text-white">{title}</p>
</div>
