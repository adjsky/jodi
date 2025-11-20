import { page, router } from "@inertiajs/svelte";
import { fromStore } from "svelte/store";

import { toaster } from "../lib/toast";

export function useFlashToast() {
    const flash = $derived(fromStore(page).current.props.flash);

    $effect(() =>
        router.on("success", () => {
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
