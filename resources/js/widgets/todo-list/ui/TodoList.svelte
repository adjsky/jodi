<script lang="ts">
    import { Check, GripVertical } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import Checkbox from "$/features/complete-todo/ui/Checkbox.svelte";
    import { complete } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import PencilNote from "$/shared/assets/pencil-note.svg";
    import { prefersLightText } from "$/shared/lib/color";
    import { tw } from "$/shared/lib/styles";
    import { toaster } from "$/shared/lib/toaster";
    import { dragHandle, dragHandleZone } from "svelte-dnd-action";

    import { useReorder } from "../api/reorder.svelte";
    import { UNGROUPED_KEY } from "../cfg/constants";
    import { optimistic, visitOptions } from "../cfg/inertia";
    import { groupTodos } from "../helpers/group-todos";
    import { editView } from "../model/view";
    import EditTodo from "./EditTodo.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        todos: App.Data.TodoDto[];
    };

    const { todos, ...rest }: Props = $props();

    const reorder = useReorder({
        onError() {
            isDragging = false;
            toaster.error(m["todos.errors.reorder"]());
        }
    });

    // svelte-ignore state_referenced_locally
    let groups = $state(groupTodos(todos));
    let isDragging = $state(false);

    $effect(() => {
        if (!isDragging && !reorder.isMutating) {
            groups = groupTodos(todos);
        }
    });

    function consider(group: string, todos: App.Data.TodoDto[]) {
        isDragging = true;
        groups = { ...groups, [group]: todos };
    }

    function finalize(group: string, todos: App.Data.TodoDto[]) {
        isDragging = false;
        groups = { ...groups, [group]: todos };
        reorder.mutate(group, todos);
    }
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center gap-1.5">
        <Check class="text-3xl" />
        <h3 class="text-lg font-bold">{m["todos.title"]()}</h3>
    </div>

    {#if todos.length == 0}
        <img
            src={PencilNote}
            width={217}
            height={256}
            alt=""
            loading="lazy"
            decoding="async"
            class="mx-auto mt-15 max-w-30"
        />
        <p class="mx-auto mt-8 max-w-3/4 text-center text-lg font-medium">
            {m["todos.no-todos"]()}
        </p>
    {:else}
        <div class="mt-4 space-y-4">
            {#each Object.entries(groups) as [group, todos] (group)}
                {#if Object.keys(groups).length == 1 && group == UNGROUPED_KEY}
                    {@render list(group, todos)}
                {:else}
                    {@const nCompleted = todos.filter(
                        (todo) => todo.completedAt != null
                    ).length}
                    <div class="mb-1 font-medium">
                        <span class="text-cream-500">
                            {nCompleted}/{todos.length}
                        </span>
                        <span class="text-cream-500">•</span>
                        <span>
                            {group == UNGROUPED_KEY
                                ? m["todos.ungrouped"]()
                                : group}
                        </span>
                    </div>
                    {@render list(group, todos)}
                {/if}
            {/each}
        </div>
    {/if}

    <EditTodo
        bind:open={
            () => editView.isOpen(),
            (v) => {
                if (!v) {
                    void editView.back();
                }
            }
        }
        todo={editView.meta}
    />
</section>

{#snippet list(group: string, todos: App.Data.TodoDto[])}
    <div
        use:dragHandleZone={{
            items: todos,
            dropTargetStyle: {}
        }}
        onconsider={(e) => consider(group, e.detail.items)}
        onfinalize={(e) => finalize(group, e.detail.items)}
    >
        {#each todos as todo (todo.id)}
            <Todo.Row
                class={["outline-none", todo.completedAt && "opacity-40"]}
            >
                {#snippet checkbox()}
                    <Checkbox
                        {...visitOptions}
                        {...optimistic.complete(todo.id)}
                        href={complete(todo.id)}
                        completedAt={todo.completedAt}
                    />
                {/snippet}
                {#snippet edit()}
                    <button
                        class="relative table w-full table-fixed text-start text-lg font-medium"
                        data-part="edit"
                        onclick={() => editView.push(todo)}
                    >
                        <span
                            class={[
                                "table-cell overflow-hidden text-ellipsis whitespace-nowrap",
                                todo.completedAt && "line-through",
                                todo.color && [
                                    "rounded-xl px-1.5",
                                    prefersLightText(todo.color) && "text-white"
                                ]
                            ]}
                            style="background: {todo.color ?? 'transparent'};"
                        >
                            {todo.title}
                        </span>
                    </button>
                {/snippet}
                {#snippet grip()}
                    <button use:dragHandle aria-label="lorem" class="shrink-0">
                        <GripVertical class="text-2xl text-cream-400" />
                    </button>
                {/snippet}
            </Todo.Row>
        {/each}
    </div>
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
