<script lang="ts">
    import { BottomSheet } from "svelte-bottom-sheet";

    import { tw } from "../lib/styles";

    import type { ClassName } from "../lib/styles";
    import type { Snippet } from "svelte";

    type Props = {
        open?: boolean;
        background: string;
        grip?: string;
        snapPoints: number[];
        defaultSnapPoint?: number;
        class?: ClassName;
        children: Snippet;
        trigger?: Snippet;
    };

    let {
        open = $bindable(false),
        background,
        grip,
        snapPoints,
        defaultSnapPoint,
        class: classname,
        children,
        trigger
    }: Props = $props();
</script>

<BottomSheet
    bind:isSheetOpen={open}
    settings={{
        maxHeight: Math.max(...snapPoints),
        snapPoints,
        startingSnapPoint: defaultSnapPoint
    }}
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
                class={tw("relative h-full w-full px-4! py-2!", classname)}
            >
                {@render children()}
            </BottomSheet.Content>
        </BottomSheet.Sheet>
    </BottomSheet.Overlay>
</BottomSheet>
