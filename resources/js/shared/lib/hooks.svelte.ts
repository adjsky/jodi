import { extract } from "runed";

import type { Getter } from "runed";

export function useLastDefined<T>(value: Getter<T | null>) {
    let lastValue = $state(extract(value));

    $effect(() => {
        const newValue = extract(value);

        if (newValue === null) return;

        lastValue = newValue;
    });

    return {
        get current() {
            return extract(value) ?? lastValue;
        },
        reset() {
            lastValue = null;
        }
    };
}
