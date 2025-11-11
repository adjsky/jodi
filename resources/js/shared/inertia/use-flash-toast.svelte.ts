import { page } from "@inertiajs/svelte";

import { toaster } from "../lib/toast";

export function useFlashToast() {
    $effect(() =>
        page.subscribe(({ props: { flash } }) => {
            if (flash.error) {
                toaster.error({ title: flash.error });
            } else if (flash.message) {
                toaster.info({ title: flash.message });
            } else if (flash.success) {
                toaster.success({ title: flash.success });
            }
        })
    );
}
