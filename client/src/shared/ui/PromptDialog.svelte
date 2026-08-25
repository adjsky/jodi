<script lang="ts">
    import { Dialog } from "@ark-ui/svelte";
    import { tw } from "#shared/lib/styles/tw.ts";

    import AppPortal from "./AppPortal.svelte";

    import type { DialogRootProps } from "@ark-ui/svelte";
    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = Pick<DialogRootProps, "onExitComplete" | "lazyMount"> &
        SvelteHTMLElements["div"] & {
            open?: boolean;
            title: string;
            portal?: boolean;
            disabled?: boolean;
            label: {
                abort: string;
                confirm: string;
            };
            children?: Snippet;
            onAbort?: VoidFunction;
            onConfirm?: VoidFunction;
        };

    let {
        open = $bindable(),
        title,
        portal = true,
        disabled,
        label,
        lazyMount,
        children,
        onAbort,
        onConfirm,
        onExitComplete,
        ...props
    }: Props = $props();
</script>

<Dialog.Root bind:open {lazyMount} {onExitComplete}>
    <AppPortal disabled={!portal}>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-[calc(100+var(--layer-index,0))] bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            {...props}
            class={tw(
                "fixed top-1/2 left-1/2 z-[calc(100+var(--layer-index,0))] w-80 -translate-1/2 rounded-4xl bg-white px-6 py-4 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-bottom",
                props.class
            )}
        >
            <Dialog.Title class="text-sm font-semibold">
                {title}
            </Dialog.Title>

            {@render children?.()}

            <div class="mt-5 flex justify-end gap-8">
                <Dialog.CloseTrigger
                    class="text-ms font-bold text-brand"
                    onclick={onAbort}
                    data-autofocus
                >
                    {label.abort}
                </Dialog.CloseTrigger>
                <button
                    {disabled}
                    class="text-ms font-bold text-brand disabled:opacity-60"
                    onclick={onConfirm}
                >
                    {label.confirm}
                </button>
            </div>
        </Dialog.Content>
    </AppPortal>
</Dialog.Root>
