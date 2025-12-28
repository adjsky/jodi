import { page, router } from "@inertiajs/svelte";
import { fromStore } from "svelte/store";

type Options = {
    showProgress?: boolean;
};

export function useSearchParams(options?: Options) {
    const { showProgress } = options ?? {};

    const props = $derived(fromStore(page).current.props);

    function update(values: Record<string, string>) {
        const url = new URL(window.location.href);

        for (const [key, value] of Object.entries(values)) {
            url.searchParams.set(key, value);
        }

        router.visit(url, {
            showProgress,
            replace: true,
            preserveScroll: true,
            preserveState: true,
            async: true,
            headers: {
                "Cache-Control": "no-cache"
            }
        });
    }

    return new Proxy({} as Record<string, string> & { update: typeof update }, {
        get(_, prop: string) {
            if (prop === "update") {
                return update;
            }

            return props.search[prop];
        },
        set(_, prop, v: string) {
            update({ [prop]: v });
            return true;
        }
    });
}
