<script lang="ts">
    import { Trash } from "@lucide/svelte";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";

    import { view } from "../model/view";

    type Props = {
        list: string[];
        selected: string | null;
        onSelect?: (name: string | null) => void;
    };

    const { list, selected, onSelect }: Props = $props();
</script>

<div class="grow">
    {#each list as name (name)}
        <div
            class="relative flex h-13.75 items-center border-cream-300 not-first:border-t"
        >
            <Checkbox
                label={name}
                checked={selected == name}
                onclick={() => {
                    if (selected == name) {
                        onSelect?.(null);
                    } else {
                        onSelect?.(name);
                    }
                }}
                class="absolute inset-0 px-2 py-0 text-lg"
            />
            <button
                type="button"
                onclick={(e) => {
                    e.preventDefault();
                    void view.push(view.name, {
                        ...view.meta,
                        [DISABLE_SHEET_DRAGGING]: true,
                        __selectcategory: { isOpen: true },
                        __categorytodelete: name
                    });
                }}
                class="absolute right-0 z-10 p-2"
            >
                <Trash class="text-xl text-red" />
            </button>
        </div>
    {/each}
</div>
