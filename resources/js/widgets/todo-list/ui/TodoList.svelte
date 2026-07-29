<script lang="ts">
    import { Check, GripVertical } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import Checkbox from "$/features/complete-todo/ui/Checkbox.svelte";
    import CompleteTodo from "$/generated/actions/App/Domain/Todo/Actions/CompleteTodo";
    import { m } from "$/paraglide/messages";
    import PencilNote from "$/shared/assets/pencil-note.svg";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import { prefersLightText } from "$/shared/lib/color/prefers-light-text";
    import { tw } from "$/shared/lib/css/tw";
    import { formatToHHMM } from "$/shared/lib/date/format-to-hh-mm";
    import { Haptics } from "$/shared/lib/haptics";
    import { toaster } from "$/shared/ui/toaster";
    import { isDeepEqual } from "remeda";
    import { untrack } from "svelte";
    import { dragHandle, dragHandleZone, TRIGGERS } from "svelte-dnd-action";

    import { useReorder } from "../api/reorder.svelte";
    import { UNGROUPED_KEY } from "../cfg/constants";
    import { optimistic, visitOptions } from "../cfg/inertia";
    import { groupTodos } from "../helpers/group-todos";
    import { id } from "../helpers/id";
    import { editView } from "../model/view";
    import EditSheet from "./EditSheet.svelte";

    import type { TodoData } from "$/entities/todo";
    import type { DndEvent } from "svelte-dnd-action";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        todos: TodoData[];
    };

    const { todos, ...rest }: Props = $props();

    const reorder = useReorder(() => todos, {
        onError() {
            isDragging = false;
            toaster.error(m["todos.errors.reorder"]());
        }
    });

    const searchParams = useSearchParams();

    $effect(() => {
        if (searchParams["target"] !== "todo") return;

        const id = searchParams["id"];
        if (!id || isNaN(Number(id))) return;

        const todo = todos.find((t) => t.id === Number(id));
        if (!todo) return;

        void editView.replace({
            meta: todo,
            search: { d: searchParams["d"] }
        });
    });

    let groups = $state(untrack(() => groupTodos(todos)));
    let isDragging = $state(false);

    $effect(() => {
        if (!isDragging && !reorder.isMutating) {
            groups = groupTodos(todos);
        }
    });

    function consider(e: CustomEvent<DndEvent<TodoData>>, group: string) {
        isDragging = true;

        if (
            e.detail.info.trigger == TRIGGERS.DRAGGED_ENTERED ||
            e.detail.info.trigger == TRIGGERS.DRAGGED_OVER_INDEX
        ) {
            if (!isDeepEqual(groups[group], e.detail.items)) {
                void Haptics.selectionChanged();
            }
        }

        groups = { ...groups, [group]: e.detail.items };
    }

    function finalize(e: CustomEvent<DndEvent<TodoData>>, group: string) {
        isDragging = false;
        groups = { ...groups, [group]: e.detail.items };
        reorder.mutate(group, e.detail.items);
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

    <EditSheet
        bind:open={() => editView.isOpen(), () => editView.back()}
        todo={editView.isOpen() ? editView.meta : null}
    />
</section>

{#snippet list(group: string, todos: TodoData[])}
    <div
        use:dragHandleZone={{
            items: todos,
            dropTargetStyle: {}
        }}
        onconsider={(e) => consider(e, group)}
        onfinalize={(e) => finalize(e, group)}
    >
        {#each todos as todo (id(todo))}
            <Todo.Row class={[todo.completedAt && "opacity-40"]}>
                {#snippet checkbox()}
                    <Checkbox
                        {...visitOptions}
                        {...optimistic.complete(todo)}
                        href={CompleteTodo(todo.id)}
                        completedAt={todo.completedAt}
                        occursAt={todo.occursAt}
                    />
                {/snippet}
                {#snippet edit()}
                    {@const time = todo.hasTime
                        ? formatToHHMM(new Date(todo.scheduledAt))
                        : null}
                    <button
                        class="relative w-full min-w-0 text-start text-lg font-medium"
                        data-part="edit"
                        onclick={() => editView.push({ meta: todo })}
                        disabled={editView.isOpen()}
                    >
                        <span
                            class={[
                                "block w-fit max-w-full truncate",
                                todo.completedAt && "line-through",
                                todo.color && [
                                    "rounded-xl px-1.5",
                                    prefersLightText(todo.color) && "text-white"
                                ]
                            ]}
                            style="background: {todo.color ?? 'transparent'};"
                        >
                            {time ? `${time} ${todo.title}` : todo.title}
                        </span>
                    </button>
                {/snippet}
                {#snippet grip()}
                    <button
                        use:dragHandle
                        aria-label="Drag"
                        class="shrink-0"
                        onpointerdown={() => {
                            void Haptics.selectionStart();
                            void Haptics.impact("medium");
                        }}
                        onpointerup={() => {
                            void Haptics.selectionEnd();
                        }}
                    >
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
