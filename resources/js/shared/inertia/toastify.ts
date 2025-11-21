import { toaster } from "../lib/toast";

import type { Page } from "@inertiajs/core";

export function toastify() {
    return {
        onSuccess({ props: { flash } }: Page) {
            if (flash.error) {
                toaster.error({ title: flash.error });
            } else if (flash.message) {
                toaster.info({ title: flash.message });
            } else if (flash.success) {
                toaster.success({ title: flash.success });
            }
        }
    };
}
