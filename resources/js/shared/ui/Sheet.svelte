<script module>
    export const DISABLE_SHEET_DRAGGING = "___sheet_disable_dragging";
</script>

<script lang="ts">
    import { BottomSheet } from "svelte-bottom-sheet";

    import { HistoryView } from "../inertia/history-view.svelte";
    import { tw } from "../lib/styles";

    import type { ClassName } from "../lib/styles";
    import type { Snippet } from "svelte";

    type Props = {
        open?: boolean;
        background: string;
        grip?: string;
        maxHeight: number;
        snapPoints: number[];
        startingSnapPoint: number;
        class?: ClassName;
        children: Snippet;
        trigger?: Snippet;
        onCloseComplete?: VoidFunction;
    };

    let {
        open = $bindable(false),
        background,
        grip,
        maxHeight,
        snapPoints,
        startingSnapPoint,
        class: classname,
        children,
        trigger,
        onCloseComplete
    }: Props = $props();

    const view = new HistoryView();
</script>

<BottomSheet
    bind:isSheetOpen={open}
    settings={{
        maxHeight,
        snapPoints,
        startingSnapPoint,
        disableDragging: Boolean(view.meta?.[DISABLE_SHEET_DRAGGING])
    }}
    onclosecomplete={onCloseComplete}
>
    {#if trigger}
        <BottomSheet.Trigger>
            {@render trigger()}
        </BottomSheet.Trigger>
    {/if}
    <BottomSheet.Overlay>
        <BottomSheet.Sheet
            style="background: {background};"
            class="flex flex-col"
        >
            {#if grip}
                <BottomSheet.Handle style="background: {background};">
                    <BottomSheet.Grip style="background: {grip};" />
                </BottomSheet.Handle>
            {/if}
            <BottomSheet.Content
                class={tw(
                    "relative flex! h-full w-full flex-col pt-2! px-safe-offset-4! pb-safe-offset-2!",
                    classname
                )}
            >
                {@render children()}
            </BottomSheet.Content>
        </BottomSheet.Sheet>
    </BottomSheet.Overlay>
</BottomSheet>
