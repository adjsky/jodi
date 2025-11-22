import { page, router } from "@inertiajs/svelte";
import { extract } from "runed";
import { fromStore } from "svelte/store";

import type { MaybeGetter } from "runed";

export function useHistoryModal(
    name: MaybeGetter<string>,
    ready?: MaybeGetter<boolean>
) {
    const url = $derived(new URL(fromStore(page).current.url, location.origin));
    const hash = $derived(`#${extract(name)}`);

    return {
        get open() {
            return url.hash == hash && (extract(ready) ?? true);
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
                if (!fromStore(page).current.__jodi_isHistoryModal) {
                    router.replace({
                        preserveScroll: true,
                        preserveState: true,
                        url: url.pathname + url.search
                    });
                } else {
                    history.back();
                }
            }
        }
    };
}
