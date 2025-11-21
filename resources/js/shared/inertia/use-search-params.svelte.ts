import { page, router } from "@inertiajs/svelte";
import { fromStore } from "svelte/store";

type Options = {
    showProgress?: boolean;
};

export function useSearchParams(options?: Options) {
    const { showProgress } = options ?? {};

    const props = $derived(fromStore(page).current.props);

    function update(values: Record<string, string>) {
        router.reload({
            data: {
                ...props.search,
                ...values
            },
            replace: true,
            showProgress
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
