/* eslint-disable @typescript-eslint/no-explicit-any */

import { router } from "@inertiajs/svelte";
import { toaster } from "$/shared/lib/toaster";

import type { PageProps, VisitCallbacks } from "@inertiajs/core";

type CommitFn = (prev: any, data: any) => any;
type Options = {
    error?: string;
    omitHash?: boolean;
    onSuccess?: (props: PageProps) => void;
};

export function optimistic(
    commit: CommitFn,
    options?: Options
): Partial<VisitCallbacks> {
    let savedProps: PageProps | null = null;
    let commitedProps: PageProps | null = null;

    return {
        async onBefore(e) {
            await router.replace({
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => options?.onSuccess?.(commitedProps!),
                props: (prev) => {
                    savedProps ??= structuredClone(prev);
                    commitedProps ??= commit(prev, e.data);
                    return { ...prev, ...commitedProps };
                },
                ...(options?.omitHash && {
                    url: location.pathname + location.search
                })
            });
        },
        onInvalid(response) {
            if (savedProps) {
                void router.replace({
                    preserveScroll: true,
                    preserveState: true,
                    props: savedProps
                });
                savedProps = null;
            }
            toaster.error(options?.error ?? response.data.message);
            return false;
        }
    };
}
