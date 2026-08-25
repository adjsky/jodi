<script lang="ts">
    import { Dialog } from "@ark-ui/svelte/dialog";
    import { m } from "$/paraglide/messages";

    import { tw } from "../lib/styles/tw";
    import AppPortal from "./AppPortal.svelte";
    import Button from "./Button.svelte";

    import type { MaybePromise } from "../lib/async/types";
    import type { DialogRootProps } from "@ark-ui/svelte/dialog";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes, SvelteHTMLElements } from "svelte/elements";

    type Props = Pick<DialogRootProps, "onExitComplete"> &
        SvelteHTMLElements["div"] & {
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
        onAbort,
        onExitComplete,
        ...props
    }: Props = $props();
</script>

<Dialog.Root bind:open role="alertdialog" {onExitComplete}>
    {#if trigger}
        <Dialog.Trigger>
            {#snippet asChild(props)}{@render trigger(props)}{/snippet}
        </Dialog.Trigger>
    {/if}
    <AppPortal disabled={!portal}>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-[calc(200+var(--layer-index,0))] bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            {...props}
            class={tw(
                "fixed top-1/2 left-1/2 z-[calc(200+var(--layer-index,0))] w-[calc(100vw-2rem)] max-w-140 -translate-1/2 rounded-4xl bg-white p-6 py-8 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-bottom",
                props.class
            )}
        >
            <Dialog.Title class="text-2xl font-bold wrap-break-word">
                {title}
            </Dialog.Title>

            {@render content?.()}

            <div class="mt-5 flex gap-2">
                <Dialog.CloseTrigger onclick={onAbort}>
                    {#snippet asChild(props)}
                        <Button
                            {...props({ onclick: onAbort })}
                            type="button"
                            variant="secondary"
                        >
                            {m["common.no"]()}
                        </Button>
                    {/snippet}
                </Dialog.CloseTrigger>
                <Button
                    type="button"
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
    </AppPortal>
</Dialog.Root>
