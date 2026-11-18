<script lang="ts">
    import { Drawer, useDrawer } from "@ark-ui/svelte";
    import { Dismissal } from "#shared/capabilities/dismissal/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";

    import type { Snippet } from "svelte";

    type Props = {
        open?: boolean;
        max_height: number;
        snap_points: number[];
        default_snap_point: number;
        children: Snippet;
        on_exit_complete?: VoidFunction;
    };

    const id = $props.id();
    let {
        open = $bindable(false),
        max_height,
        snap_points,
        default_snap_point,
        children,
        on_exit_complete
    }: Props = $props();

    let is_animating = $state(false);
    let sheet_content: HTMLElement | null = $state(null);
    let user_content: HTMLDivElement | null = $state(null);

    const drawer = useDrawer({
        get id() {
            return id;
        },
        get open() {
            return open;
        },
        onOpenChange(details) {
            if (details.open !== open) {
                open = details.open;
            }
        },
        get defaultSnapPoint() {
            return default_snap_point;
        },
        get snapPoints() {
            return snap_points;
        },
        get closeOnInteractOutside() {
            return !is_animating;
        },
        initialFocusEl() {
            const matched = user_content?.querySelector<HTMLElement>(
                "[data-autofocus], [autofocus]"
            );

            if (!matched) {
                return user_content;
            }

            return matched;
        }
    });

    Dismissal.use(
        () => open,
        () => (open = false)
    );
</script>

<Drawer.RootProvider value={drawer} onExitComplete={on_exit_complete}>
    <Drawer.Backdrop
        class="fixed inset-0 z-[calc(100+var(--layer-index,0))] bg-cream-950/60"
        onanimationstart={() => {
            is_animating = true;
        }}
        onanimationend={() => {
            is_animating = false;
        }}
    />
    <Drawer.Positioner
        class="fixed inset-0 z-[calc(100+var(--layer-index,0))] flex items-end justify-center"
    >
        <Drawer.Content
            {@attach VirtualKeyboard.retain_focus({ blur_on_scroll: true })}
            bind:ref={sheet_content}
            class="relative flex size-full min-h-0 flex-col rounded-t-2xl bg-white shadow-none outline-none"
            style="max-height: {max_height * 100}svh"
        >
            <Drawer.Grabber
                class="flex h-(--grabber-height) w-full shrink-0 touch-none items-center justify-center select-none"
            >
                <Drawer.GrabberIndicator class="h-1 w-10 rounded-full bg-cream-300" />
            </Drawer.Grabber>
            <div
                bind:this={user_content}
                class="relative flex min-h-0 flex-col pt-2 px-safe-offset-4 pb-safe-offset-2 outline-none"
                tabindex="-1"
                data-scope="drawer"
                data-part="user-content"
                onfocusin={(event) => {
                    if (!VirtualKeyboard.overlays_content) return;
                    if (!(event.target instanceof HTMLElement)) return;
                    if (!event.target.closest("[data-expand-sheet]")) return;

                    const owner = event.target.closest(
                        '[data-scope="drawer"][data-part="content"]'
                    );

                    if (owner !== sheet_content) return;

                    drawer().setSnapPoint(Math.max(...snap_points));
                }}
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
        --user-content-transition-duration: 0.5s;

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
        height: calc(100% - var(--drawer-translate-y, 0px) - var(--grabber-height));

        min-height: 0;
        flex-shrink: 0;

        transition: height var(--user-content-transition-duration) cubic-bezier(0.32, 0.72, 0, 1);
    }

    @keyframes slide-in-bottom {
        from {
            --user-content-transition-duration: 0s;
            transform: translate3d(0, 100%, 0);
        }
        to {
            --user-content-transition-duration: 0s;
            transform: translate3d(var(--drawer-translate-x, 0), var(--drawer-translate-y, 0), 0);
        }
    }

    @keyframes slide-out-bottom {
        from {
            transform: translate3d(var(--drawer-translate-x, 0), var(--drawer-translate-y, 0), 0);
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
