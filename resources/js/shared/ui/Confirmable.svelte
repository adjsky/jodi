<script lang="ts">
    import { Dialog } from "@ark-ui/svelte/dialog";
    import { Portal } from "@ark-ui/svelte/portal";
    import { m } from "$/paraglide/messages";

    import Button from "./Button.svelte";

    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        title: string;
        children: Snippet<[() => HTMLButtonAttributes]>;
        onConfirm?: () => boolean | void;
        onAbort?: VoidFunction;
    };

    const { title, children, onConfirm, onAbort }: Props = $props();

    let open = $state(false);
</script>

<Dialog.Root bind:open>
    <Dialog.Trigger>
        {#snippet asChild(props)}{@render children(props)}{/snippet}
    </Dialog.Trigger>
    <Portal>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-50 bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Positioner>
            <Dialog.Content
                class={[
                    "fixed top-1/2 left-1/2 z-50 w-[calc(100vw-2rem)] max-w-140 -translate-1/2 rounded-4xl bg-white p-6 py-8 duration-300",
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
                        onclick={() => {
                            if (onConfirm?.()) {
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
