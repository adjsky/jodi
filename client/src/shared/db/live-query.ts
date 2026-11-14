import { liveQuery as _liveQuery } from "dexie";
import { fromStore, readable } from "svelte/store";

export type LiveQuery<T> = { current: T | undefined };

export function liveQuery<T>(query: () => T | Promise<T>): LiveQuery<T> {
    const store = readable<T | undefined>(undefined, (set) => {
        const subscription = _liveQuery(query).subscribe({
            next: set
        });

        return () => subscription.unsubscribe();
    });

    return fromStore(store);
}
