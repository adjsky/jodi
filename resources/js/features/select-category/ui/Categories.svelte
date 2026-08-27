<script lang="ts">
    import { Trash } from "@lucide/svelte";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";

    import { view } from "../model/view";

    import type { CategoryData } from "$/entities/todo";

    type Props = {
        isLoading?: boolean;
        list?: CategoryData[];
        selectedId?: number | null;
        onSelect?: (category: CategoryData | null) => void;
    };

    const { isLoading, list, selectedId, onSelect }: Props = $props();
</script>

<div
    class="flex min-h-0 grow flex-col overflow-y-auto overscroll-contain pb-safe safe-area-keyboard"
>
    {#if isLoading}
        {#each Array.from({ length: 5 }) as _, idx (idx)}
            {@render row()}
        {/each}
    {:else}
        {#each list as category (category.id)}
            {@render row(category)}
        {/each}
    {/if}
</div>

{#snippet row(category?: CategoryData)}
    <div
        class="relative flex h-13.75 shrink-0 items-center border-cream-300 not-first:border-t"
    >
        {#if !category}
            <Skeleton class="text-lg" grow inline={false} />
        {:else}
            <Checkbox
                label={category.name}
                checked={selectedId == category.id}
                onclick={() => {
                    if (selectedId == category.id) {
                        onSelect?.(null);
                    } else {
                        onSelect?.(category);
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
                            __selectcategory: { isOpen: true },
                            __categorytodelete: category
                        }
                    });
                }}
                class="absolute right-0 z-10 p-2"
            >
                <Trash class="text-xl text-red" />
            </button>
        {/if}
    </div>
{/snippet}
