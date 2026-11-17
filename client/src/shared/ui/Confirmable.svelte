<script lang="ts">
    import { Dialog } from "@ark-ui/svelte/dialog";
    import { m } from "#paraglide/messages.js";
    import { Dismissal } from "#shared/capabilities/dismissal/mod.svelte.ts";
    import { tw } from "#shared/lib/styles/tw.ts";

    import AppPortal from "./AppPortal.svelte";
    import Button from "./Button.svelte";

    import type { Snippet } from "svelte";
    import type { HTMLAttributes, SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"] & {
        title: string;
        open?: boolean;
        portal?: boolean;
        trigger?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        content?: Snippet;
        on_confirm?: VoidFunction;
        on_abort?: VoidFunction;
        on_exit_complete?: VoidFunction;
    };

    let {
        title,
        open = $bindable(false),
        portal = true,
        trigger,
        content,
        on_confirm,
        on_abort,
        on_exit_complete,
        ...props
    }: Props = $props();

    Dismissal.use(
        () => open,
        () => (open = false)
    );
</script>

<Dialog.Root bind:open role="alertdialog" onExitComplete={on_exit_complete}>
    {#if trigger}
        <Dialog.Trigger>
            {#snippet asChild(props)}{@render trigger(props)}{/snippet}
        </Dialog.Trigger>
    {/if}
    <AppPortal disabled={!portal}>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-[calc(100+var(--layer-index,0))] bg-cream-950/60 animation-duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            {...props}
            class={tw(
                "fixed top-1/2 left-1/2 z-[calc(100+var(--layer-index,0))] w-[calc(100vw-2rem)] max-w-140 -translate-1/2 rounded-4xl bg-white p-6 py-8 animation-duration-300",
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
                <Dialog.CloseTrigger onclick={on_abort}>
                    {#snippet asChild(props)}
                        <Button {...props({ onclick: on_abort })} type="button" variant="secondary">
                            {m["common.no"]()}
                        </Button>
                    {/snippet}
                </Dialog.CloseTrigger>
                <Button type="button" onclick={on_confirm}>
                    {m["common.yes"]()}
                </Button>
            </div>
        </Dialog.Content>
    </AppPortal>
</Dialog.Root>
