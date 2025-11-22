import { router } from "@inertiajs/svelte";

import type {
    ActiveVisit,
    PageProps,
    PendingVisit,
    RequestPayload
} from "@inertiajs/core";

type CommitFn<T extends RequestPayload> = (
    prev: PageProps,
    data: T
) => Partial<PageProps>;

type RollbackFn = () => void;

export function optimistic<T extends RequestPayload>(
    commit: CommitFn<T>,
    _rollback: RollbackFn
) {
    const handlers = {
        onBefore(e: PendingVisit<T>) {
            router.push({
                preserveScroll: true,
                preserveState: true,
                props: (prev) => ({
                    ...prev,
                    ...commit(prev, e.data)
                })
            });
        },
        onFinish(_e: ActiveVisit<T>) {}
    };

    return merge(handlers);
}

function merge<
    T extends Record<string, unknown>,
    R = T & {
        [K in keyof T as Lowercase<K & string>]: T[K];
    }
>(v: T): R {
    return Object.entries(v).reduce((acc, [k, v]) => {
        acc[k as keyof R] = v as never;
        acc[k.toLowerCase() as keyof R] = v as never;
        return acc;
    }, {} as R);
}
