import { extract, watch } from "runed";

import type { Getter } from "runed";

export class LastMatching<T> {
    #lastMatch: T | null;

    constructor(value: Getter<T>, matcher: (value: T) => boolean) {
        const initialValue = extract(value);

        this.#lastMatch = $state(matcher(initialValue) ? initialValue : null);

        watch(
            () => [extract(value)],
            () => {
                const newValue = extract(value);
                if (matcher(newValue)) this.#lastMatch = newValue;
            },
            { lazy: true }
        );
    }

    get current(): T | null {
        return this.#lastMatch;
    }

    reset(): void {
        this.#lastMatch = null;
    }
}
