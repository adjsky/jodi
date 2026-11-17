import { Debounced } from "runed";

import type { Getter, MaybeGetter } from "runed";

export function use_loading_debounce(
    getter: Getter<boolean>,
    wait: MaybeGetter<number> = 200
): Debounced<boolean> {
    const debounced = new Debounced(getter, wait);

    $effect(() => {
        if (!getter()) {
            void debounced.updateImmediately();
        }
    });

    return debounced;
}
