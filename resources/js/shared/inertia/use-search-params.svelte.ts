import { page, router } from "@inertiajs/svelte";
import { fromStore } from "svelte/store";

import type { VisitOptions } from "@inertiajs/core";

type Options = {
    showProgress?: boolean;
};

export function useSearchParams(options?: Options) {
    const { showProgress } = options ?? {};

    const url = $derived(new URL(fromStore(page).current.url, location.origin));

    function update(values: Record<string, string>, options?: VisitOptions) {
        const url = new URL(window.location.href);

        for (const [key, value] of Object.entries(values)) {
            url.searchParams.set(key, value);
        }

        return router.visit(url, {
            ...options,
            showProgress,
            replace: true,
            preserveScroll: true,
            preserveState: true,
            async: true,
            headers: {
                ...options?.headers,
                "Cache-Control": "no-cache"
            }
        });
    }

    return new Proxy({} as Record<string, string> & { update: typeof update }, {
        get(_, prop: string) {
            if (prop === "update") {
                return update;
            }

            return url.searchParams.get(prop);
        },
        set(_, prop, v: string) {
            void update({ [prop]: v });
            return true;
        }
    });
}
