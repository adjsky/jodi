import { router } from "@inertiajs/svelte";
import { reorder } from "$/generated/actions/App/Http/Controllers/TodoController";
import { useDebounce } from "runed";

import { UNGROUPED_KEY } from "../cfg/constants";

type Options = {
    onError?: VoidFunction;
    onSuccess?: VoidFunction;
};

type TodoBatches = Record<string, App.Data.TodoDto[]>;

export function useReorder(options?: Options) {
    const { onError, onSuccess } = options ?? {};

    let isMutating = $state(false);
    const todoBatches: TodoBatches = {};

    function mutate(group: string, todos: App.Data.TodoDto[]) {
        todoBatches[group] = todos;
        isMutating = true;
        void _mutate({ ...todoBatches });
    }

    const _mutate = useDebounce((batches: TodoBatches) => {
        void router.visit(reorder(), {
            async: false,
            data: {
                todos: Object.entries(batches)
                    .map(([group, todos]) =>
                        todos.map((t, idx) => ({
                            id: t.id,
                            name: t.title,
                            position: idx + 1,
                            category: group == UNGROUPED_KEY ? null : group
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
