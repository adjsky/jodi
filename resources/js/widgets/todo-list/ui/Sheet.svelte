<script lang="ts">
    import { BottomSheet } from "svelte-bottom-sheet";

    import type { Snippet } from "svelte";

    type Props = {
        open: boolean;
        content: Snippet;
        trigger: Snippet;
    };

    let { open = $bindable(), content, trigger }: Props = $props();
</script>

<BottomSheet
    bind:isSheetOpen={open}
    settings={{
        maxHeight: 0.95,
        snapPoints: [0.6, 0.95],
        startingSnapPoint: 0.6
    }}
>
    <BottomSheet.Trigger>
        {@render trigger()}
    </BottomSheet.Trigger>
    <BottomSheet.Overlay>
        <BottomSheet.Sheet class="!bg-white">
            <BottomSheet.Handle class="!bg-white" />
            <BottomSheet.Content class="w-full !px-4 !pt-2 !pb-5">
                {@render content()}
            </BottomSheet.Content>
        </BottomSheet.Sheet>
    </BottomSheet.Overlay>
</BottomSheet>
