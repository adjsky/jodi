import { toaster } from "../../lib/toast";

import type { VisitCallbacks } from "@inertiajs/core";

type Options = {
    invalid?: string;
};

export function toastify(options?: Options): Partial<VisitCallbacks> {
    return {
        onSuccess({ props: { flash } }) {
            if (flash.error) {
                toaster.error({ title: flash.error });
            } else if (flash.message) {
                toaster.info({ title: flash.message });
            } else if (flash.success) {
                toaster.success({ title: flash.success });
            }
        },
        onInvalid(response) {
            toaster.error({ title: options?.invalid ?? response.data.message });
            return false;
        }
    };
}
