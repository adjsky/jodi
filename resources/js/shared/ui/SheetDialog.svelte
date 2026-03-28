<script lang="ts">
    import { Dialog, Portal } from "@ark-ui/svelte";
    import { ChevronLeft } from "@lucide/svelte";

    import type { DialogRootProps } from "@ark-ui/svelte";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = Pick<DialogRootProps, "onExitComplete" | "lazyMount"> & {
        open: boolean;
        title?: string;
        height: number;
        portal?: boolean;
        trigger?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        children: Snippet;
    };

    let {
        open = $bindable(),
        title,
        height,
        trigger,
        children,
        portal = false,
        ...props
    }: Props = $props();
</script>

<Dialog.Root bind:open {...props}>
    {#if trigger}
        <Dialog.Trigger>
            {#snippet asChild(props)}
                {@render trigger(props)}
            {/snippet}
        </Dialog.Trigger>
    {/if}

    <Dialog.Backdrop
        class={[
            "fixed inset-0 z-[calc(100+var(--layer-index))] bg-cream-950/60 duration-300",
            "data-[state=closed]:animate-out data-[state=closed]:fade-out",
            "data-[state=open]:animate-in data-[state=open]:fade-in"
        ]}
    />
    <Portal disabled={!portal}>
        <Dialog.Content
            class={[
                "fixed inset-x-0 bottom-0 z-[calc(100+var(--layer-index))] flex flex-col rounded-t-2xl bg-white px-4 py-3 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:slide-in-from-bottom"
            ]}
            style="height: {height}%;"
        >
            {#if title}
                <div class="relative flex items-center justify-between">
                    <button
                        class="p-2"
                        type="button"
                        onclick={() => (open = false)}
                        data-autofocus
                    >
                        <ChevronLeft class="text-4xl" />
                    </button>
                    <span
                        class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
                    >
                        {title}
                    </span>
                </div>
            {/if}

            {@render children()}
        </Dialog.Content>
    </Portal>
</Dialog.Root>
