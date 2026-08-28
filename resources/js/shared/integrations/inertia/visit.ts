import { router } from "@inertiajs/svelte";

import type {
    ReloadOptions,
    UrlMethodPair,
    VisitOptions
} from "@inertiajs/core";

export function visit(
    href: string | URL | UrlMethodPair,
    options: VisitOptions = {}
): Promise<boolean> {
    return new Promise((resolve) => {
        let succeeded = false;

        router.visit(href, {
            ...options,
            onBefore(pendingVisit) {
                const result = options.onBefore?.(pendingVisit);

                if (result === false) {
                    resolve(false);
                }

                return result;
            },
            onSuccess(page) {
                succeeded = true;
                return options.onSuccess?.(page);
            },
            onFinish(finishedVisit) {
                options.onFinish?.(finishedVisit);
                resolve(succeeded);
            }
        });
    });
}

export function reload(options: ReloadOptions = {}): Promise<boolean> {
    return new Promise((resolve) => {
        let succeeded = false;

        router.reload({
            ...options,
            onBefore(pendingVisit) {
                const result = options.onBefore?.(pendingVisit);

                if (result === false) {
                    resolve(false);
                }

                return result;
            },
            onSuccess(page) {
                succeeded = true;
                return options.onSuccess?.(page);
            },
            onFinish(finishedVisit) {
                options.onFinish?.(finishedVisit);
                resolve(succeeded);
            }
        });
    });
}
