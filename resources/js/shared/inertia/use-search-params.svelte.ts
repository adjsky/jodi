import { page, router } from "@inertiajs/svelte";
import { fromStore } from "svelte/store";

import type { VisitOptions } from "@inertiajs/core";

type Options = {
    showProgress?: boolean;
    push?: boolean;
};

export function useSearchParams(options?: Options) {
    const { showProgress, push } = options ?? {};

    function update(values: Record<string, string>, options?: VisitOptions) {
        const url = new URL(window.location.href);

        for (const [key, value] of Object.entries(values)) {
            url.searchParams.set(key, value);
        }

        return router.visit(url, {
            ...options,
            showProgress,
            replace: options?.replace ?? !push,
            preserveScroll: true,
            preserveState: true,
            async: true,
            headers: {
                ...options?.headers,
                "Cache-Control": "no-cache"
            }
        });
    }

    // Keep this deriveds hell to stabilize reactivity (so that `sp` variable
    // reruns on actual query changes).
    const p = $derived(fromStore(page).current);
    const url = $derived(new URL(p.url, window.location.href));
    const search = $derived(url.search);
    const sp = $derived(new URLSearchParams(search));

    return new Proxy({} as Record<string, string> & { update: typeof update }, {
        get(_, prop: string) {
            if (prop === "update") {
                return update;
            }

            return sp.get(prop);
        },
        set(_, prop, v: string) {
            void update({ [prop]: v });
            return true;
        }
    });
}
