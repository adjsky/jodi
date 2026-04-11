import { router } from "@inertiajs/svelte";
import { parseAbsolute, toCalendarDate } from "@internationalized/date";
import ReorderTodos from "$/generated/actions/App/Domain/Todo/Actions/ReorderTodos";
import { TIMEZONE } from "$/shared/cfg/constants";
import { extract, useDebounce } from "runed";

import { UNGROUPED_KEY } from "../cfg/constants";

import type { TodoData } from "$/entities/todo";
import type { Getter } from "runed";

type Options = {
    onError?: VoidFunction;
    onSuccess?: VoidFunction;
};

type TodoBatches = Record<string, TodoData[]>;

export function useReorder(todos: Getter<TodoData[]>, options?: Options) {
    const { onError, onSuccess } = options ?? {};

    let isMutating = $state(false);
    const todoBatches: TodoBatches = {};

    const categoryIdByName = $derived(
        extract(todos).reduce(
            (acc, t) => {
                if (!t.category) {
                    return acc;
                }

                acc[t.category.name] = t.category.id;

                return acc;
            },
            {} as Record<string, number>
        )
    );

    function mutate(group: string, todos: TodoData[]) {
        todoBatches[group] = todos;
        isMutating = true;
        void _mutate({ ...todoBatches });
    }

    const _mutate = useDebounce((batches: TodoBatches) => {
        void router.visit(ReorderTodos(), {
            async: false,
            data: {
                todos: Object.entries(batches)
                    .map(([group, todos]) =>
                        todos.map((t, idx) => ({
                            id: t.id,
                            name: t.title,
                            position: idx + 1,
                            categoryId:
                                group == UNGROUPED_KEY
                                    ? null
                                    : categoryIdByName[group],
                            date: toCalendarDate(
                                parseAbsolute(t.scheduledAt, TIMEZONE)
                            ).toString()
                        }))
                    )
                    .flat()
            },
            only: ["todos"],
            replace: true,
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            showProgress: false,
            onInvalid() {
                onError?.();
                return false;
            },
            onFinish() {
                isMutating = false;
                for (const group of Object.keys(batches)) {
                    delete todoBatches[group];
                }
            },
            onSuccess
        });
    }, 250);

    return {
        mutate,
        get isMutating() {
            return isMutating;
        }
    };
}
