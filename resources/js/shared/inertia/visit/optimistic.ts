/* eslint-disable @typescript-eslint/no-explicit-any */

import { router } from "@inertiajs/svelte";
import { toaster } from "$/shared/lib/toast";

import type { PageProps, VisitCallbacks } from "@inertiajs/core";

type CommitFn = (prev: any, data: any) => any;
type Options = {
    error?: string;
    onBefore?: VoidFunction;
    preserveUrl?: "without-hash";
};

export function optimistic(
    commit: CommitFn,
    options?: Options
): Partial<VisitCallbacks> {
    let savedProps: PageProps | null = null;

    return {
        onBefore(e) {
            router.replace({
                preserveScroll: true,
                preserveState: true,
                props: (prev) => {
                    if (savedProps == null) {
                        savedProps = structuredClone(prev);
                    }
                    return {
                        ...prev,
                        ...commit(prev, e.data)
                    };
                },
                ...(options?.preserveUrl == "without-hash" && {
                    url: location.pathname + location.search
                })
            });
        },
        onInvalid(response) {
            if (savedProps) {
                router.replace({
                    preserveScroll: true,
                    preserveState: true,
                    props: savedProps
                });
                savedProps = null;
            }
            toaster.error({ title: options?.error ?? response.data.message });
            return false;
        }
    };
}
