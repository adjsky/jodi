<script lang="ts">
    import { fromAbsolute, toZoned } from "@internationalized/date";
    import { Check, GripVertical } from "@lucide/svelte";
    import { Todo } from "#entities/todo/mod.ts";
    import { SelectedEntry } from "#features/choose-entry/mod.ts";
    import { Checkbox } from "#features/complete-todo/mod.ts";
    import { m } from "#paraglide/messages.js";
    import PencilNote from "#shared/assets/pencil-note.svg";
    import { Haptics } from "#shared/capabilities/haptics/mod.ts";
    import { needs_light_contrast } from "#shared/lib/color/needs-light-contrast.ts";
    import { format_to_hhmm } from "#shared/lib/date/format-to-hh-mm.ts";
    import { tw } from "#shared/lib/styles/tw.ts";
    import { isDeepEqual } from "remeda";
    import { untrack } from "svelte";
    import { dragHandle, dragHandleZone, TRIGGERS } from "svelte-dnd-action";

    import { UNGROUPED_KEY } from "../cfg/constants";
    import { group_todos } from "../helpers/group-todos";
    import { id } from "../helpers/id";
    import EditSheet from "./EditSheet.svelte";

    import type { TodoOccurrence } from "#entities/todo/mod.ts";
    import type { DndEvent } from "svelte-dnd-action";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        todos: TodoOccurrence[];
    };

    const { todos, ...rest }: Props = $props();

    // $effect(() => {
    //     if (searchParams["target"] !== "todo") return;

    //     const sid = searchParams["id"];
    //     if (!sid || isNaN(Number(sid))) return;

    //     const todo = todos.find((t) => t.id === Number(sid));
    //     if (!todo) return;

    //     void editView.replace({
    //         meta: { __todo: { id: id(todo) } },
    //         search: { d: searchParams["d"] }
    //     });
    // });

    let edit_open = $state(false);

    let groups = $derived(group_todos(todos));
    // let isDragging = $state(false);

    const todo = $derived.by(() => {
        const selected = SelectedEntry.current;
        if (!selected) return null;

        const todo = todos.find((t) => id(t) === selected.id);
        if (!todo) return null;

        return todo;
    });

    // $effect(() => {
    //     if (!isDragging && !reorder.isMutating) {
    //         groups = group_todos(todos);
    //     }
    // });

    // function consider(e: CustomEvent<DndEvent<TodoData>>, group: string) {
    //     isDragging = true;

    //     if (
    //         e.detail.info.trigger === TRIGGERS.DRAGGED_ENTERED ||
    //         e.detail.info.trigger === TRIGGERS.DRAGGED_OVER_INDEX
    //     ) {
    //         if (!isDeepEqual(groups[group], e.detail.items)) {
    //             void Haptics.selection_changed();
    //         }
    //     }

    //     groups = { ...groups, [group]: e.detail.items };
    // }

    // function finalize(e: CustomEvent<DndEvent<TodoData>>, group: string) {
    //     isDragging = false;
    //     groups = { ...groups, [group]: e.detail.items };
    //     reorder.mutate(group, e.detail.items);
    // }
</script>

<EditSheet bind:open={edit_open} {todo} />

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center gap-1.5">
        <Check class="text-3xl" />
        <h3 class="text-lg font-bold">{m["todos.title"]()}</h3>
    </div>

    {#if todos.length === 0}
        <img
            src={PencilNote}
            width={217}
            height={256}
            alt=""
            decoding="async"
            class="mx-auto mt-15 max-w-30"
        />
        <p class="mx-auto mt-8 max-w-3/4 text-center text-lg font-medium">
            {m["todos.no-todos"]()}
        </p>
    {:else}
        <div class="mt-1.75 space-y-4">
            {#each Object.entries(groups) as [group, todos] (group)}
                {#if Object.keys(groups).length === 1 && group === UNGROUPED_KEY}
                    {@render list(group, todos)}
                {:else}
                    {@const n_completed = todos.filter((todo) => todo.completed_at !== null).length}
                    <div class="mb-1 font-medium">
                        <span class="text-cream-500">
                            {n_completed}/{todos.length}
                        </span>
                        <span class="text-cream-500">•</span>
                        <span>
                            {group === UNGROUPED_KEY ? m["todos.ungrouped"]() : group}
                        </span>
                    </div>
                    {@render list(group, todos)}
                {/if}
            {/each}
        </div>
    {/if}
</section>

{#snippet list(group: string, todos: TodoOccurrence[])}
    <div
        use:dragHandleZone={{
            items: todos,
            dropTargetStyle: {}
        }}
        onconsider={(e) => consider(e, group)}
        onfinalize={(e) => finalize(e, group)}
    >
        {#each todos as todo (id(todo))}
            <Todo.Row class={[todo.completed_at && "opacity-40"]}>
                {#snippet checkbox()}
                    <Checkbox
                        id={todo.id}
                        completed_at={todo.completed_at}
                        occurs_at={todo.occurs_at}
                    />
                {/snippet}
                {#snippet edit()}
                    {@const time = todo.has_time
                        ? format_to_hhmm(todo.scheduled_at.toDate())
                        : null}
                    {@const title = todo.title || m["todos.no-title"]()}
                    <button
                        class="relative w-full min-w-0 text-start text-lg font-medium"
                        data-part="edit"
                        onclick={() => {
                            edit_open = true;
                            SelectedEntry.update({
                                type: "todo",
                                id: id(todo)
                            });
                        }}
                        disabled={edit_open}
                    >
                        <span
                            class={[
                                "block w-fit max-w-full truncate",
                                todo.completed_at && "line-through",
                                todo.color && [
                                    "rounded-xl px-1.5",
                                    needs_light_contrast(todo.color) && "text-white"
                                ]
                            ]}
                            style="background: {todo.color ?? 'transparent'};"
                        >
                            {time ? `${time} ${title}` : title}
                        </span>
                    </button>
                {/snippet}
                {#snippet grip()}
                    <button
                        use:dragHandle
                        aria-label="Drag"
                        class="shrink-0"
                        onpointerdown={() => {
                            void Haptics.selection_start();
                            void Haptics.impact("medium");
                        }}
                        onpointerup={() => {
                            void Haptics.selection_end();
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
