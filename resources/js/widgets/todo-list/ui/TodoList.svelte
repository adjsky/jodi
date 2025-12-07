<script lang="ts">
    import { Check, GripVertical } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import Checkbox from "$/features/complete-todo/ui/Checkbox.svelte";
    import { m } from "$/paraglide/messages";
    import PencilNote from "$/shared/assets/pencil-note.svg";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { tw } from "$/shared/lib/styles";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
    import { groupBy } from "remeda";

    import AddTodo from "./AddTodo.svelte";
    import EditTodo from "./EditTodo.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        loading: boolean;
        todos: App.Data.TodoDto[];
    };

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

    const editView = new HistoryView<App.Data.TodoDto>("edit-todo");
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1.5">
            <Check class="text-3xl" />
            <h3 class="text-lg font-bold">{m["todos.title"]()}</h3>
        </div>
        <AddTodo {loading} />
    </div>

    {#if todos?.length == 0}
        <img
            src={PencilNote}
            width={217}
            height={256}
            alt=""
            loading="lazy"
            decoding="async"
            class="mx-auto mt-24 max-w-30"
        />
        <p class="mx-auto mt-8 max-w-3/4 text-center text-lg font-medium">
            {m["todos.no-todos"]()}
        </p>
    {:else}
        <div class="mt-3">
            {#each Object.entries(groups) as [group, todos], idx (idx)}
                {#if Object.keys(groups).length == 1 && group == m["todos.ungrouped"]()}
                    {@render list(todos)}
                {:else}
                    {@const completed = todos.filter(
                        (todo) => todo.completedAt != null
                    ).length}
                    <div class="mb-1 font-medium not-first:mt-4">
                        <span class="text-cream-500">
                            {completed}/{todos.length}
                        </span>
                        <span class="text-cream-500">•</span>
                        <span>{group}</span>
                    </div>
                    {@render list(todos)}
                {/if}
            {/each}
        </div>
    {/if}

    <EditTodo todo={editView.meta} onclose={() => editView.close()} />
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
                        editView.open(todo);
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
                                todo.completedAt && "line-through",
                                todo.color && "rounded-xl px-1"
                            ]}
                            style={todo.color
                                ? `background: ${todo.color};`
                                : null}
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
