<script lang="ts">
    import { Trash } from "@lucide/svelte";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";

    import { view } from "../model/view";

    import type { CategoryData } from "$/entities/todo";

    type Props = {
        list: { id: number; name: string }[];
        selectedId: number | null;
        onSelect?: (category: CategoryData | null) => void;
    };

    const { list, selectedId, onSelect }: Props = $props();
</script>

<div class="grow">
    {#each list as { id, name } (id)}
        <div
            class="relative flex h-13.75 items-center border-cream-300 not-first:border-t"
        >
            <Checkbox
                label={name}
                checked={selectedId == id}
                onclick={() => {
                    if (selectedId == id) {
                        onSelect?.(null);
                    } else {
                        onSelect?.({ id, name });
                    }
                }}
                class="absolute inset-0 py-0 ps-2 pe-9 text-lg"
            />
            <button
                type="button"
                onclick={(e) => {
                    e.preventDefault();
                    void view.push(view.name, {
                        meta: {
                            ...view.meta,
                            [DISABLE_SHEET_DRAGGING]: true,
                            __selectcategory: { isOpen: true },
                            __categorytodelete: { id, name }
                        }
                    });
                }}
                class="absolute right-0 z-10 p-2"
            >
                <Trash class="text-xl text-red" />
            </button>
        </div>
    {/each}
</div>
