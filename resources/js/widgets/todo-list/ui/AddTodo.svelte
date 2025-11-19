<script lang="ts">
    import { Plus } from "@lucide/svelte";
    import { Form } from "$/features/add-todo";
    import { BottomSheet } from "svelte-bottom-sheet";
</script>

<BottomSheet settings={{ maxHeight: 0.5 }}>
    <BottomSheet.Trigger
        class="flex size-8 items-center justify-center rounded-lg border border-cream-950 bg-white"
    >
        <Plus class="text-xl" />
    </BottomSheet.Trigger>
    <BottomSheet.Overlay>
        <BottomSheet.Sheet class="!bg-white">
            <BottomSheet.Content class="p-4 pb-5">
                <Form />
            </BottomSheet.Content>
        </BottomSheet.Sheet>
    </BottomSheet.Overlay>
</BottomSheet>
