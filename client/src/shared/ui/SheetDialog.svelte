<script lang="ts">
    import { Dialog } from "@ark-ui/svelte";
    import { ChevronLeft } from "@lucide/svelte";
    import { tw } from "#shared/lib/styles/tw.ts";

    import AppPortal from "./AppPortal.svelte";

    import type { DialogRootProps } from "@ark-ui/svelte";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes, SvelteHTMLElements } from "svelte/elements";

    type Props = Pick<
        DialogRootProps,
        "onExitComplete" | "lazyMount" | "unmountOnExit"
    > &
        SvelteHTMLElements["div"] & {
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
        lazyMount,
        unmountOnExit,
        onExitComplete,
        ...props
    }: Props = $props();
</script>

<Dialog.Root bind:open {lazyMount} {unmountOnExit} {onExitComplete}>
    {#if trigger}
        <Dialog.Trigger>
            {#snippet asChild(props)}
                {@render trigger(props)}
            {/snippet}
        </Dialog.Trigger>
    {/if}

    <AppPortal disabled={!portal}>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-[calc(100+var(--layer-index,0))] bg-cream-950/60",
                "data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=closed]:ease-in-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:duration-500 data-[state=open]:ease-[cubic-bezier(0.32,0.72,0,1)] data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            {...props}
            class={tw(
                "fixed inset-x-0 bottom-0 z-[calc(100+var(--layer-index,0))] flex flex-col rounded-t-2xl bg-white pt-3 px-safe-offset-4 pb-safe-offset-5",
                "data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=closed]:ease-in-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:duration-500 data-[state=open]:ease-[cubic-bezier(0.32,0.72,0,1)] data-[state=open]:slide-in-from-bottom",
                props.class
            )}
            style="height: {height}%;"
        >
            {#if title}
                <div class="relative flex items-center justify-between">
                    <button
                        class="p-2"
                        type="button"
                        onclick={() => (open = false)}
                    >
                        <ChevronLeft class="text-4xl" />
                    </button>
                    <span
                        class="absolute left-1/2 -translate-x-1/2 text-xl font-bold"
                    >
                        {title}
                    </span>
                </div>
            {/if}

            {@render children()}
        </Dialog.Content>
    </AppPortal>
</Dialog.Root>
