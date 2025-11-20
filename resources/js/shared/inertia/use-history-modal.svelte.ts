import { page, router } from "@inertiajs/svelte";
import { fromStore } from "svelte/store";

export function useHistoryModal(name: string) {
    const url = $derived(new URL(fromStore(page).current.url, location.origin));
    const hash = `#${name}`;

    return {
        get open() {
            return url.hash == hash;
        },
        set open(v: boolean) {
            if (v) {
                router.push({
                    preserveScroll: true,
                    preserveState: true,
                    url: url.pathname + url.search + hash,
                    __jodi_isHistoryModal: true
                });
            } else {
                history.back();
            }
        }
    };
}
