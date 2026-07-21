<script lang="ts">
    import { Drawer, useDrawer } from "@ark-ui/svelte";

    import { DEFER_FRAMES } from "../cfg/constants";
    import { useDeferUntilNextFrame } from "../lib/hooks.svelte";

    import type { DrawerRootProps } from "@ark-ui/svelte";
    import type { Snippet } from "svelte";

    type Props = Pick<DrawerRootProps, "onExitComplete"> & {
        open?: boolean;
        maxHeight: number;
        snapPoints: number[];
        defaultSnapPoint: number;
        children: Snippet;
    };

    const id = $props.id();
    let {
        open = $bindable(false),
        maxHeight,
        snapPoints,
        defaultSnapPoint,
        children,
        ...drawerRootProps
    }: Props = $props();

    let contentRef: HTMLElement | null = $state(null);

    // Defer opening the drawer on page load until the next available frame so
    // that Zag can calculate content height properly, otherwise the content
    // will be rendered according to the largest available snap point.
    const deferred = useDeferUntilNextFrame(DEFER_FRAMES.SHEET);

    const drawer = useDrawer({
        get id() {
            return id;
        },
        get open() {
            return deferred.ready && open;
        },
        onOpenChange(details) {
            open = details.open;
        },
        get defaultSnapPoint() {
            return defaultSnapPoint;
        },
        get snapPoints() {
            return snapPoints;
        },
        initialFocusEl() {
            return contentRef;
        }
    });
</script>

<Drawer.RootProvider value={drawer} {...drawerRootProps}>
    <Drawer.Backdrop
        class="fixed inset-0 z-[calc(100+var(--layer-index,0))] bg-cream-950/60"
    />
    <Drawer.Positioner
        class="fixed inset-0 z-[calc(100+var(--layer-index,0))] flex items-end justify-center"
    >
        <Drawer.Content
            bind:ref={contentRef}
            class="relative flex size-full flex-col rounded-t-2xl bg-white shadow-none outline-none"
            style="max-height: {maxHeight * 100}svh"
        >
            <Drawer.Grabber
                class="flex h-(--grabber-height) w-full shrink-0 touch-none items-center justify-center select-none"
            >
                <Drawer.GrabberIndicator
                    class="h-1 w-10 rounded-full bg-cream-300"
                />
            </Drawer.Grabber>
            <div
                data-scope="drawer"
                data-part="user-content"
                class="relative flex flex-col pt-2 px-safe-offset-4 pb-safe-offset-2"
                data-no-drag
            >
                {@render children()}
            </div>
        </Drawer.Content>
    </Drawer.Positioner>
</Drawer.RootProvider>

<style>
    :global([data-scope="drawer"][data-part="backdrop"]) {
        &[data-state="open"] {
            animation: fade-in 0.5s cubic-bezier(0.32, 0.72, 0, 1);
        }

        &[data-state="closed"] {
            animation: fade-out 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    }

    :global([data-scope="drawer"][data-part="content"]) {
        --grabber-height: 2.25rem;

        transition: transform 0.5s cubic-bezier(0.32, 0.72, 0, 1);

        :global(&:is([data-swiping], [data-dragging])) {
            & > [data-part="user-content"] {
                transition-duration: 0s;
            }
        }

        &[data-state="open"] {
            animation: slide-in-bottom 0.5s cubic-bezier(0.32, 0.72, 0, 1);
        }

        &[data-state="closed"] {
            animation: slide-out-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        &::after {
            content: "";
            position: absolute;
            inset-inline: 0;
            top: 100%;
            height: 3rem;
            background-color: inherit;
            pointer-events: none;
        }
    }

    :global([data-scope="drawer"][data-part="user-content"]) {
        height: calc(
            100% - var(--drawer-translate-y, 0px) - var(--grabber-height)
        );

        min-height: 0;
        flex-shrink: 0;

        transition: height 0.5s cubic-bezier(0.32, 0.72, 0, 1);
    }

    @keyframes slide-in-bottom {
        from {
            transform: translate3d(0, 100%, 0);
        }
        to {
            transform: translate3d(
                var(--drawer-translate-x, 0),
                var(--drawer-translate-y, 0),
                0
            );
        }
    }

    @keyframes slide-out-bottom {
        from {
            transform: translate3d(
                var(--drawer-translate-x, 0),
                var(--drawer-translate-y, 0),
                0
            );
        }
        to {
            transform: translate3d(0, 100%, 0);
        }
    }

    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fade-out {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
</style>
