import { toaster } from "$/shared/ui/toaster";

import type {
    ActiveVisit,
    FormComponentOptimisticCallback,
    FormDataConvertible,
    OptimisticCallback,
    VisitCallbacks,
    VisitOptions
} from "@inertiajs/core";
import type { AppPageProps } from "$/globals";

type OptimisticOptions = Partial<VisitCallbacks> & {
    rollbackError: string;
};

type OptimisticReturn = Pick<
    VisitCallbacks,
    "onOptimisticRollback" | "onHttpException" | "onNetworkError"
> &
    Pick<VisitOptions, "optimistic">;

export type InertiaFormData = Record<string, FormDataConvertible>;

export function optimistic<P = AppPageProps>(
    commit: OptimisticCallback<P>,
    options: OptimisticOptions
): OptimisticReturn;

export function optimistic<P = AppPageProps, F = InertiaFormData>(
    commit: FormComponentOptimisticCallback<P, F>,
    options: OptimisticOptions
): OptimisticReturn;

export function optimistic(
    commit: (...args: unknown[]) => unknown,
    {
        rollbackError,
        onHttpException,
        onNetworkError,
        onOptimisticRollback,
        ...callbacks
    }: OptimisticOptions
): OptimisticReturn {
    return {
        ...callbacks,
        optimistic: commit as never,
        onOptimisticRollback(visit: ActiveVisit) {
            if (!visit.cancelled && !visit.interrupted) {
                toaster.error(rollbackError);
            }
            onOptimisticRollback?.(visit);
        },
        onHttpException(response) {
            onHttpException?.(response);
            return false;
        },
        onNetworkError(error) {
            onNetworkError?.(error);
            return false;
        }
    };
}
