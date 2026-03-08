<script lang="ts">
    import { Dialog, Portal } from "@ark-ui/svelte";

    import type { DialogRootProps } from "@ark-ui/svelte";
    import type { Snippet } from "svelte";

    type Props = Pick<DialogRootProps, "onExitComplete"> & {
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
        children,
        onAbort,
        onConfirm,
        ...dialogRootProps
    }: Props = $props();

    let closeTrigger = $state<HTMLButtonElement | null>(null);
</script>

<Dialog.Root bind:open {...dialogRootProps} initialFocusEl={() => closeTrigger}>
    <Portal disabled={!portal}>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-150 bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Positioner>
            <Dialog.Content
                class={[
                    "fixed top-1/2 left-1/2 z-150 w-80 -translate-1/2 rounded-4xl bg-white px-6 py-4 duration-300",
                    "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom",
                    "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-bottom"
                ]}
            >
                <Dialog.Title class="text-sm font-semibold">
                    {title}
                </Dialog.Title>

                {@render children?.()}

                <div class="mt-5 flex justify-end gap-8">
                    <Dialog.CloseTrigger
                        bind:ref={closeTrigger}
                        class="text-ms font-bold text-brand"
                        onclick={onAbort}
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
        </Dialog.Positioner>
    </Portal>
</Dialog.Root>
