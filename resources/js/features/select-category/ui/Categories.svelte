<script lang="ts">
    import { Check, Trash } from "@lucide/svelte";
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
            <button
                type="button"
                class="absolute inset-0 flex items-center gap-2 px-2 text-start text-lg font-medium"
                onclick={() => {
                    if (selected == name) {
                        onSelect?.(null);
                    } else {
                        onSelect?.(name);
                    }
                }}
            >
                <span
                    class={[
                        "flex size-5.5 items-center justify-center rounded-full border border-cream-950",
                        selected == name && "bg-cream-950"
                    ]}
                >
                    {#if selected == name}
                        <Check class="shrink-0 text-md text-white" />
                    {/if}
                </span>
                <span>{name}</span>
            </button>
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
