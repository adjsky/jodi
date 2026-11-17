<script lang="ts">
    import { Presence } from "@ark-ui/svelte";
    import { trackDismissableElement } from "@zag-js/dismissable";
    import { Dismissal } from "#shared/capabilities/dismissal/mod.svelte.ts";
    import { tw } from "#shared/lib/styles/tw.ts";
    import AppPortal from "#shared/ui/AppPortal.svelte";

    import { context } from "../model/context.ts";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"] & {
        open?: boolean;
        portal?: boolean;
        lazy_mount?: boolean;
        unmount_on_exit?: boolean;
        on_exit_complete?: VoidFunction;
    };

    const id = $props.id();
    let {
        children,
        open = $bindable(false),
        portal = true,
        lazy_mount = true,
        unmount_on_exit = true,
        ...props
    }: Props = $props();

    context.set({
        close() {
            open = false;
        }
    });

    Dismissal.use(
        () => open,
        () => (open = false)
    );
</script>

<AppPortal disabled={!portal}>
    <Presence
        {@attach (element) => {
            return trackDismissableElement(element, {
                type: "screen-view",
                defer: true,
                pointerBlocking: true,
                onDismiss() {
                    open = false;
                },
                onRequestDismiss(e) {
                    e.preventDefault();
                },
                onFocusOutside(e) {
                    e.preventDefault();
                },
                onInteractOutside(e) {
                    e.preventDefault();
                },
                onPointerDownOutside(e) {
                    e.preventDefault();
                }
            });
        }}
        {...props}
        id="screen-view:{id}:overlay"
        present={open}
        class={tw(
            "fixed inset-0 z-[calc(100+var(--layer-index,0))] flex flex-col overflow-hidden bg-cream-50 pt-safe-offset-3 pb-safe-offset-5 safe-area-keyboard",
            props.class
        )}
        lazyMount={lazy_mount}
        unmountOnExit={unmount_on_exit}
        {children}
    />
</AppPortal>
