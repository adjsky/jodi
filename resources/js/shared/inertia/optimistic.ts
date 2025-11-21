import { router } from "@inertiajs/svelte";

import type { PendingVisit } from "@inertiajs/core";

export function optimistic() {
    return {
        onBefore(_e: PendingVisit) {
            router.replace({
                preserveScroll: true,
                preserveState: true,
                props: (prev) => ({
                    ...prev
                    // todos: [...prev.todos, { id: 1293213, ...e.data }]
                })
            });
        }
    };
}
