<script lang="ts">
    import { Presence } from "@ark-ui/svelte";
    import { trackDismissableElement } from "@zag-js/dismissable";
    import { tw } from "#shared/lib/styles/tw.ts";
    import AppPortal from "#shared/ui/AppPortal.svelte";

    import { context } from "../model/context.ts";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"] & {
        open?: boolean;
        portal?: boolean;
        lazyMount?: boolean;
        unmountOnExit?: boolean;
        onExitComplete?: VoidFunction;
    };

    const id = $props.id();
    let {
        children,
        open = $bindable(false),
        portal = true,
        lazyMount = true,
        unmountOnExit = true,
        ...props
    }: Props = $props();

    context.set({
        get open() {
            return open;
        },
        set open(v) {
            open = v;
        }
    });
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
        id="floating-view:{id}:overlay"
        present={open}
        class={tw(
            "fixed inset-0 z-[calc(100+var(--layer-index,0))] flex flex-col overflow-hidden bg-cream-50 pt-safe-offset-3 pb-safe-offset-5 safe-area-keyboard",
            props.class
        )}
        {children}
        {lazyMount}
        {unmountOnExit}
    />
</AppPortal>
