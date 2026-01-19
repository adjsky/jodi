import { CollisionPriority } from "@dnd-kit/abstract";
import { DragDropManager, Droppable } from "@dnd-kit/dom";
import { isSortable, Sortable } from "@dnd-kit/dom/sortable";
import { move } from "@dnd-kit/helpers";
import { router } from "@inertiajs/svelte";
import { reorder } from "$/generated/actions/App/Http/Controllers/TodoController";
import { m } from "$/paraglide/messages";
import { groupBy } from "remeda";
import { extract } from "runed";

import type { Getter } from "runed";
import type { Attachment } from "svelte/attachments";

export function useSortableTodos(todos: Getter<App.Data.TodoDto[]>) {
    const manager = new DragDropManager();

    let groups = $derived(
        groupBy(
            extract(todos),
            ({ category }) => category || m["todos.ungrouped"]()
        )
    );

    $effect(() =>
        manager.monitor.addEventListener("dragover", async (e) => {
            groups = move(groups, e);
        })
    );

    $effect(() =>
        manager.monitor.addEventListener(
            "dragend",
            async ({ suspend, operation: { source } }) => {
                suspend().abort();

                if (!isSortable(source)) {
                    return;
                }

                const { id, index, group } = source.sortable;

                void router.visit(reorder(Number(id)), {
                    replace: true,
                    preserveScroll: true,
                    preserveState: true,
                    preserveUrl: true,
                    showProgress: false,
                    only: ["todos"],
                    data: {
                        position: index + 1,
                        category: group == m["todos.ungrouped"]() ? null : group
                    }
                });
            }
        )
    );

    const sortable =
        (id: number, group: string, index: number): Attachment =>
        (element) => {
            const sortable = new Sortable(
                { id, index, group, element, type: "todo", accept: "todo" },
                manager
            );
            return () => sortable?.unregister();
        };

    const droppable =
        (id: string): Attachment =>
        (element) => {
            const droppable = new Droppable(
                {
                    id,
                    element,
                    type: "group",
                    accept: "todo",
                    collisionPriority: CollisionPriority.Low
                },
                manager
            );
            return () => droppable.unregister();
        };

    return {
        attachments: { sortable, droppable },
        get groups() {
            return groups;
        }
    };
}
