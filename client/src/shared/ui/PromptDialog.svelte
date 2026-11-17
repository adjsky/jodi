<script lang="ts">
    import { Dialog } from "@ark-ui/svelte";
    import { Dismissal } from "#shared/capabilities/dismissal/mod.svelte.ts";
    import { tw } from "#shared/lib/styles/tw.ts";

    import AppPortal from "./AppPortal.svelte";

    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"] & {
        open?: boolean;
        title: string;
        portal?: boolean;
        disabled?: boolean;
        label: {
            abort: string;
            confirm: string;
        };
        lazy_mount?: boolean;
        children?: Snippet;
        on_abort?: VoidFunction;
        on_confirm?: VoidFunction;
        on_exit_complete?: VoidFunction;
    };

    let {
        open = $bindable(false),
        title,
        portal = true,
        disabled,
        label,
        lazy_mount,
        children,
        on_abort,
        on_confirm,
        on_exit_complete,
        ...props
    }: Props = $props();

    Dismissal.use(
        () => open,
        () => (open = false)
    );
</script>

<Dialog.Root bind:open lazyMount={lazy_mount} onExitComplete={on_exit_complete}>
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
                "fixed top-1/2 left-1/2 z-[calc(100+var(--layer-index,0))] w-80 -translate-1/2 rounded-4xl bg-white px-6 py-4 animation-duration-300",
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
                    onclick={on_abort}
                    data-autofocus
                >
                    {label.abort}
                </Dialog.CloseTrigger>
                <button
                    {disabled}
                    class="text-ms font-bold text-brand disabled:opacity-60"
                    onclick={on_confirm}
                >
                    {label.confirm}
                </button>
            </div>
        </Dialog.Content>
    </AppPortal>
</Dialog.Root>
