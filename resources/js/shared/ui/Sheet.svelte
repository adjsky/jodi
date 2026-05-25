<script module>
    export const DISABLE_SHEET_DRAGGING = "___sheet_disable_dragging";
</script>

<script lang="ts">
    import { Capacitor } from "@capacitor/core";
    import { BottomSheet } from "svelte-bottom-sheet";

    import { HistoryView } from "../inertia/history-view.svelte";
    import { tw } from "../lib/styles";

    import type { ClassName } from "../lib/styles";
    import type { Snippet } from "svelte";

    type Props = {
        open?: boolean;
        maxHeight: number;
        snapPoints: number[];
        defaultSnapPoint: number;
        class?: ClassName;
        children: Snippet;
        trigger?: Snippet;
        onExitComplete?: VoidFunction;
    };

    let {
        open = $bindable(false),
        maxHeight,
        snapPoints,
        defaultSnapPoint,
        class: classname,
        children,
        trigger,
        onExitComplete
    }: Props = $props();

    let sheet = $state<ReturnType<typeof BottomSheet> | null>(null);

    const view = new HistoryView();
</script>

<BottomSheet
    bind:this={sheet}
    bind:isSheetOpen={open}
    settings={{
        maxHeight,
        snapPoints,
        startingSnapPoint: defaultSnapPoint,
        disableDragging: Boolean(view.meta?.[DISABLE_SHEET_DRAGGING])
    }}
    onclosecomplete={onExitComplete}
>
    {#if trigger}
        <BottomSheet.Trigger>
            {@render trigger()}
        </BottomSheet.Trigger>
    {/if}
    <BottomSheet.Overlay>
        <BottomSheet.Sheet class="flex flex-col bg-white!">
            <BottomSheet.Handle class="bg-white!">
                <BottomSheet.Grip class="bg-cream-300!" />
            </BottomSheet.Handle>
            <BottomSheet.Content
                class={tw(
                    "relative flex! h-full w-full flex-col pt-2! px-safe-offset-4! pb-safe-offset-2!",
                    classname
                )}
                onfocusin={(e) => {
                    if (!Capacitor.isNativePlatform()) return;
                    const target = e.target as HTMLElement;
                    if (target.matches("[data-expand-sheet]")) {
                        sheet?.setSnapPoint(1);
                    }
                }}
            >
                {@render children()}
            </BottomSheet.Content>
        </BottomSheet.Sheet>
    </BottomSheet.Overlay>
</BottomSheet>
