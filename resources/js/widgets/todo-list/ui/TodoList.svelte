<script lang="ts">
    import { Check, ChevronDown, GripVertical, Plus } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { m } from "$/paraglide/messages";
    import { tw } from "$/shared/lib/styles";
    import { groupBy } from "remeda";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = WithClassName<
        SvelteHTMLElements["section"],
        {
            todos: App.Data.TodoDto[];
        }
    >;

    const { todos, ...rest }: Props = $props();
    const groups = $derived(
        groupBy(
            todos.sort((a, b) =>
                a.category
                    ? b.category
                        ? a.category.localeCompare(b.category)
                        : -1
                    : 1
            ),
            ({ category }) => category ?? m["todos.ungrouped"]()
        )
    );
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1">
            <Check class="text-2xl" />
            <h3 class="font-semibold">{m["todos.title"]()}</h3>
        </div>
        <button
            class="flex size-8 items-center justify-center rounded-lg border border-cream-950 bg-white"
        >
            <Plus class="text-xl" />
        </button>
    </div>
    <div class="mt-3 space-y-3">
        {#each Object.entries(groups) as [group, todos], idx (idx)}
            {@const completed = todos.filter(
                (todo) => todo.completedAt != null
            ).length}
            <div class="flex items-center justify-between not-first:mt-5">
                <div class="text-sm font-medium">
                    <span class="text-cream-500">
                        {completed}/{todos.length}
                    </span>
                    <span class="text-cream-500">•</span>
                    <span>{group}</span>
                </div>
                <button><ChevronDown class="text-xl" /></button>
            </div>
            {#each todos as todo (todo.id)}
                <Todo.Row>
                    {#snippet checkbox()}
                        <button
                            aria-label="lorem"
                            class="size-4.5 shrink-0 rounded-sm border border-cream-950"
                        ></button>
                    {/snippet}
                    {#snippet edit()}
                        <button
                            class="flex w-full flex-col items-start justify-start gap-1"
                        >
                            <span class="table w-full table-fixed">
                                <span
                                    class="table-cell overflow-hidden text-start text-ms font-medium text-ellipsis whitespace-nowrap"
                                >
                                    {todo.title}
                                </span>
                            </span>
                            <span class="h-px w-full bg-cream-200"></span>
                        </button>
                    {/snippet}
                    {#snippet grip()}
                        <button aria-label="lorem" class="shrink-0">
                            <GripVertical class="text-xl text-cream-400" />
                        </button>
                    {/snippet}
                </Todo.Row>
            {/each}
        {/each}
    </div>
</section>
