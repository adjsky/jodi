<script lang="ts">
    import { Dialog } from "@ark-ui/svelte/dialog";
    import { Portal } from "@ark-ui/svelte/portal";
    import { m } from "$/paraglide/messages";

    import Button from "./Button.svelte";

    import type { MaybePromise } from "../lib/types";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        title: string;
        open?: boolean;
        portal?: boolean;
        trigger?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        content?: Snippet;
        onConfirm?: () => MaybePromise<boolean | void>;
        onAbort?: VoidFunction;
    };

    let {
        title,
        open = $bindable(false),
        portal = true,
        trigger,
        content,
        onConfirm,
        onAbort
    }: Props = $props();
</script>

<Dialog.Root bind:open role="alertdialog">
    {#if trigger}
        <Dialog.Trigger>
            {#snippet asChild(props)}{@render trigger(props)}{/snippet}
        </Dialog.Trigger>
    {/if}
    <Portal disabled={!portal}>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-[calc(150+var(--layer-index))] bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            class={[
                "fixed top-1/2 left-1/2 z-[calc(150+var(--layer-index))] w-[calc(100vw-2rem)] max-w-140 -translate-1/2 rounded-4xl bg-white p-6 py-8 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-bottom"
            ]}
        >
            <Dialog.Title class="text-2xl font-bold">
                {title}
            </Dialog.Title>

            {@render content?.()}

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
    </Portal>
</Dialog.Root>
