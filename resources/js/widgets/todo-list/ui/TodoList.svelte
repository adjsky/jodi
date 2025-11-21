<script lang="ts">
    import { Check, ChevronDown, GripVertical } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { m } from "$/paraglide/messages";
    import { tw } from "$/shared/lib/styles";
    import { groupBy } from "remeda";

    import AddTodo from "./AddTodo.svelte";
    import EditTodo from "./EditTodo.svelte";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = WithClassName<
        SvelteHTMLElements["section"],
        {
            loading: boolean;
            todos: App.Data.TodoDto[];
        }
    >;

    const { todos, loading, ...rest }: Props = $props();
    const groups = $derived.by(() => {
        if (loading) {
            return {
                [m["todos.ungrouped"]()]: Array.from(
                    { length: 5 },
                    (_, idx) => ({
                        id: idx
                    })
                ) as App.Data.TodoDto[]
            };
        }

        return groupBy(
            todos,
            ({ category }) => category ?? m["todos.ungrouped"]()
        );
    });
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1">
            <Check class="text-2xl" />
            <h3 class="font-semibold">{m["todos.title"]()}</h3>
        </div>
        <AddTodo {loading} />
    </div>
    <div class="mt-3 space-y-4">
        {#each Object.entries(groups) as [group, todos], idx (idx)}
            {#if Object.keys(groups).length == 1 && group == m["todos.ungrouped"]()}
                {@render list(todos)}
            {:else}
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
                {@render list(todos)}
            {/if}
        {/each}
    </div>
</section>

{#snippet list(todos: App.Data.TodoDto[])}
    {#each todos as todo (todo.id)}
        <Todo.Row>
            {#snippet checkbox()}
                <button
                    disabled={loading}
                    aria-label="lorem"
                    class="size-4.5 shrink-0 rounded-full border border-cream-950"
                ></button>
            {/snippet}
            {#snippet edit()}
                <EditTodo {todo} {loading} />
            {/snippet}
            {#snippet grip()}
                <button disabled={loading} aria-label="lorem" class="shrink-0">
                    <GripVertical class="text-xl text-cream-400" />
                </button>
            {/snippet}
        </Todo.Row>
    {/each}
{/snippet}
