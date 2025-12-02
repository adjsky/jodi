<script lang="ts">
    import { Check, ChevronDown, GripVertical } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import Checkbox from "$/features/complete-todo/ui/Checkbox.svelte";
    import { m } from "$/paraglide/messages";
    import { tw } from "$/shared/lib/styles";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
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
                    { length: 7 },
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

    let editingTodoId = $state<number | null>(null);
    let editModalOpen = $state(false);
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1.5">
            <Check class="text-3xl" />
            <h3 class="text-lg font-bold">{m["todos.title"]()}</h3>
        </div>
        <AddTodo {loading} />
    </div>
    <div class="mt-3 space-y-4.5">
        {#each Object.entries(groups) as [group, todos], idx (idx)}
            {#if Object.keys(groups).length == 1 && group == m["todos.ungrouped"]()}
                {@render list(todos)}
            {:else}
                {@const completed = todos.filter(
                    (todo) => todo.completedAt != null
                ).length}
                <div class="flex items-center justify-between not-first:mt-5">
                    <div class="font-medium">
                        <span class="text-cream-500">
                            {completed}/{todos.length}
                        </span>
                        <span class="text-cream-500">•</span>
                        <span>{group}</span>
                    </div>
                    <button><ChevronDown class="text-2xl" /></button>
                </div>
                {@render list(todos)}
            {/if}
        {/each}
    </div>

    <EditTodo
        {loading}
        bind:open={editModalOpen}
        todo={todos?.find(({ id }) => editingTodoId == id) ?? null}
    />
</section>

{#snippet list(todos: App.Data.TodoDto[])}
    {#each todos as todo (todo.id)}
        <Todo.Row class={todo.completedAt && "opacity-40"}>
            {#snippet checkbox()}
                <Checkbox {loading} {todo} />
            {/snippet}
            {#snippet edit()}
                <button
                    disabled={loading}
                    class="relative table w-full table-fixed text-start text-lg font-medium outline-none"
                    data-part="edit"
                    onclick={() => {
                        editingTodoId = todo.id;
                        editModalOpen = true;
                    }}
                >
                    {#if loading}
                        <Skeleton
                            inline
                            style="width: {Math.random() * 100 + 100}px"
                        />
                    {:else}
                        <span
                            class={[
                                "table-cell overflow-hidden text-ellipsis whitespace-nowrap",
                                todo.completedAt && "line-through"
                            ]}
                        >
                            {todo.title}
                        </span>
                    {/if}
                </button>
            {/snippet}
            {#snippet grip()}
                <button disabled={loading} aria-label="lorem" class="shrink-0">
                    <GripVertical class="text-2xl text-cream-400" />
                </button>
            {/snippet}
        </Todo.Row>
    {/each}
{/snippet}

<style>
    [data-part="edit"]::after {
        content: "";

        position: absolute;
        left: 0;
        bottom: -4px;

        width: 100%;
        height: 1px;
        border-radius: 1px;

        background: var(--color-cream-200);
    }
</style>
