import { Debounced } from "runed";

import type { Getter, MaybeGetter } from "runed";

export function useLoadingDebounce(
    getter: Getter<boolean>,
    wait?: MaybeGetter<number>
) {
    const debounced = new Debounced(getter, wait);

    $effect(() => {
        if (!getter()) {
            void debounced.updateImmediately();
        }
    });

    return debounced;
}
