import { extract, watch } from "runed";

import type { Getter } from "runed";

export class LastMatching<T> {
    #last_match: T | null;

    constructor(value: Getter<T>, matcher: (value: T) => boolean) {
        const initial_value = extract(value);

        this.#last_match = $state(matcher(initial_value) ? initial_value : null);

        watch(
            () => [extract(value)],
            () => {
                const new_value = extract(value);
                if (matcher(new_value)) this.#last_match = new_value;
            },
            { lazy: true }
        );
    }

    get current(): T | null {
        return this.#last_match;
    }

    reset(): void {
        this.#last_match = null;
    }
}
