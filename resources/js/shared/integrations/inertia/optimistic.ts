import { toaster } from "$/shared/ui/toaster";

import type {
    ActiveVisit,
    FormComponentOptimisticCallback,
    FormDataConvertible,
    OptimisticCallback,
    VisitCallbacks,
    VisitOptions
} from "@inertiajs/core";

type OptimisticOptions = Partial<
    Exclude<VisitCallbacks, "onOptimisticRollback">
> & {
    error: string;
};

type OptimisticReturn = Pick<VisitCallbacks, "onOptimisticRollback"> &
    Pick<VisitOptions, "optimistic">;

export type InertiaFormData = Record<string, FormDataConvertible>;

export function optimistic<P>(
    commit: OptimisticCallback<P>,
    options: OptimisticOptions
): OptimisticReturn;

export function optimistic<P, F>(
    commit: FormComponentOptimisticCallback<P, F>,
    options: OptimisticOptions
): OptimisticReturn;

export function optimistic(
    commit: (...args: unknown[]) => unknown,
    { error, ...callbacks }: OptimisticOptions
): OptimisticReturn {
    return {
        ...callbacks,
        optimistic: commit as never,
        onOptimisticRollback(visit: ActiveVisit) {
            if (!visit.cancelled && !visit.interrupted) {
                toaster.error(error);
            }
        }
    };
}
