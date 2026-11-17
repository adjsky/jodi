<script lang="ts">
    import { Trash } from "@lucide/svelte";
    import Checkbox from "#shared/ui/Checkbox.svelte";

    import type { Category } from "#entities/todo/mod.ts";

    type Props = {
        list: Category[];
        current: string | null;
        on_select?: (id: string | null) => void;
        on_delete?: (id: string) => void;
    };

    const { list, current, on_select, on_delete }: Props = $props();
</script>

<div
    class="flex min-h-0 grow flex-col overflow-y-auto overscroll-contain pb-safe safe-area-keyboard"
>
    {#each list as c (c.id)}
        <div class="relative flex h-14 shrink-0 items-center border-cream-300 not-first:border-t">
            <Checkbox
                label={c.name}
                checked={current === c.id}
                onclick={() => {
                    if (current === c.id) {
                        on_select?.(null);
                    } else {
                        on_select?.(c.id);
                    }
                }}
                class="absolute inset-0 py-0 ps-2 pe-9 text-lg"
            />
            <button
                type="button"
                onclick={() => on_delete?.(c.id)}
                class="absolute right-0 z-10 p-2"
            >
                <Trash class="text-xl text-red" />
            </button>
        </div>
    {/each}
</div>
