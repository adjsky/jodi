<script lang="ts">
    import { Drawer, useDrawer } from "@ark-ui/svelte";
    import { Capacitor } from "@capacitor/core";

    import type { Snippet } from "svelte";

    type Props = {
        open?: boolean;
        maxHeight: number;
        snapPoints: number[];
        defaultSnapPoint: number;
        children: Snippet;
        onExitComplete?: VoidFunction;
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

    const drawer = useDrawer({
        get id() {
            return id;
        },
        get open() {
            return open;
        },
        onOpenChange(details) {
            open = details.open;
        },
        get defaultSnapPoint() {
            return defaultSnapPoint;
        },
        get snapPoints() {
            return snapPoints;
        }
    });
</script>

<Drawer.RootProvider value={drawer} {...drawerRootProps}>
    <Drawer.Backdrop
        class={[
            "fixed inset-0 z-100 bg-cream-950/60 duration-500",
            "data-[state=closed]:animate-out data-[state=closed]:fade-out",
            "data-[state=open]:animate-in data-[state=open]:fade-in"
        ]}
    />
    <Drawer.Positioner
        class="fixed inset-0 z-100 flex items-end justify-center"
    >
        <Drawer.Content
            class={[
                "pointer-events-auto relative flex size-full flex-col rounded-t-2xl bg-white shadow-none duration-500 ease-out outline-none",
                "data-[state=closed]:animate-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:slide-in-from-bottom"
            ]}
            style="max-height: {maxHeight * 100}%"
            onfocusin={(e) => {
                if (!Capacitor.isNativePlatform()) return;

                const target = e.target as HTMLElement;
                if (target.matches("[data-expand-sheet]")) {
                    drawer().setSnapPoint(1);
                }
            }}
        >
            <Drawer.Grabber
                class="flex w-full shrink-0 touch-none items-center justify-center py-4 select-none"
            >
                <Drawer.GrabberIndicator
                    class="h-1 w-10 rounded-full bg-cream-300"
                />
            </Drawer.Grabber>
            <div class="h-full pt-2 px-safe-offset-4 pb-safe-offset-2">
                {@render children()}
            </div>
        </Drawer.Content>
    </Drawer.Positioner>
</Drawer.RootProvider>

<style>
    :global([data-scope="drawer"][data-part="content"]) {
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
</style>
