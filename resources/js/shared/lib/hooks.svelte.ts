import { extract } from "runed";

import type { Getter } from "runed";

export function useLastMatching<T>(
    value: Getter<T>,
    matcher: (value: T) => boolean
) {
    const initialValue = extract(value);

    let lastMatch: T | null = $state(
        matcher(initialValue) ? initialValue : null
    );

    $effect(() => {
        const newValue = extract(value);
        if (matcher(newValue)) lastMatch = newValue;
    });

    return {
        get current() {
            return lastMatch;
        },
        reset() {
            lastMatch = null;
        }
    };
}
