import { liveQuery as _liveQuery } from "dexie";
import { extract, watch } from "runed";

import type { MaybeGetter } from "runed";

export type LiveQuery<T> = { current: T | undefined };

export function liveQuery<A, T>(
    args: MaybeGetter<A>,
    query: (args: A) => T | Promise<T>
): LiveQuery<T> {
    let current = $state.raw<T>();

    watch(
        () => extract(args),
        (args) => {
            const subscription = _liveQuery(() => query(args)).subscribe({
                next: (r) => {
                    current = r;
                },
                error: (e) => console.error("[liveQuery] Query failed: ", e)
            });

            return () => subscription.unsubscribe();
        }
    );

    return {
        get current() {
            return current;
        }
    };
}
