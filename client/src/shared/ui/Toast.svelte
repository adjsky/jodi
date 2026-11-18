<script lang="ts">
    import { CircleAlert, CircleCheck, Info } from "@lucide/svelte";
    import { trackDismissableBranch } from "@zag-js/dismissable";

    type Props = {
        type: "info" | "error" | "success";
        title: string;
        description?: string;
        action?: { label: string; onclick?: VoidFunction };
    };

    const { type, title, description, action }: Props = $props();
</script>

<div
    {@attach trackDismissableBranch}
    class="flex w-full items-center gap-2 rounded-lt bg-cream-800 px-3 py-2.5"
>
    {#if !action}
        <div class="shrink-0 text-3xl">
            {#if type === "info"}
                <Info class="text-yellow" />
            {:else if type === "error"}
                <CircleAlert class="text-red" />
            {:else if type === "success"}
                <CircleCheck class="text-green" />
            {/if}
        </div>
    {/if}
    <div class="grow">
        <p class="font-semibold text-white">{title}</p>
        {#if description}
            <p class="text-sm font-medium text-cream-50">{description}</p>
        {/if}
    </div>
    {#if action}
        <button
            onclick={action.onclick}
            class={[
                "shrink-0 rounded-lg px-2.5 py-1.5 text-sm font-semibold text-cream-800",
                type === "info" && "bg-yellow",
                type === "error" && "bg-red",
                type === "success" && "bg-green"
            ]}
        >
            {action.label}
        </button>
    {/if}
</div>
