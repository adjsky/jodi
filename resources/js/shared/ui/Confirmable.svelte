<script lang="ts">
    import { Dialog } from "@ark-ui/svelte/dialog";
    import { Portal } from "@ark-ui/svelte/portal";
    import { m } from "$/paraglide/messages";

    import Button from "./Button.svelte";

    import type { MaybePromise } from "../lib/types";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        title: string;
        open?: boolean;
        children: Snippet<[() => HTMLButtonAttributes]>;
        onConfirm?: () => MaybePromise<boolean | void>;
        onAbort?: VoidFunction;
        onOpen?: (open: boolean) => void;
    };

    let {
        title,
        open = $bindable(false),
        children,
        onConfirm,
        onAbort,
        onOpen
    }: Props = $props();
</script>

<Dialog.Root bind:open onOpenChange={({ open }) => onOpen?.(open)}>
    <Dialog.Trigger>
        {#snippet asChild(props)}{@render children(props)}{/snippet}
    </Dialog.Trigger>
    <Portal>
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
                    "fixed top-1/2 left-1/2 z-150 w-[calc(100vw-2rem)] max-w-140 -translate-1/2 rounded-4xl bg-white p-6 py-8 duration-300",
                    "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom",
                    "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-bottom"
                ]}
            >
                <Dialog.Title class="text-2xl font-bold">
                    {title}
                </Dialog.Title>
                <div class="mt-5 flex gap-2">
                    <Dialog.CloseTrigger onclick={onAbort}>
                        {#snippet asChild(props)}
                            <Button
                                {...props({ onclick: onAbort })}
                                variant="secondary"
                            >
                                {m["common.no"]()}
                            </Button>
                        {/snippet}
                    </Dialog.CloseTrigger>
                    <Button
                        onclick={async () => {
                            if (await onConfirm?.()) {
                                open = false;
                            }
                        }}
                    >
                        {m["common.yes"]()}
                    </Button>
                </div>
            </Dialog.Content>
        </Dialog.Positioner>
    </Portal>
</Dialog.Root>
