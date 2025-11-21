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
        <BottomSheet.Sheet class="flex flex-col !bg-white">
            <BottomSheet.Handle class="!bg-white">
                <BottomSheet.Grip class="!bg-cream-200" />
            </BottomSheet.Handle>
            <BottomSheet.Content class="h-full w-full !px-4 !pt-2 !pb-6">
                {@render content()}
            </BottomSheet.Content>
        </BottomSheet.Sheet>
    </BottomSheet.Overlay>
</BottomSheet>
