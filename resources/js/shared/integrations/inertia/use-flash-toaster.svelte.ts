import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";
import { toaster } from "$/shared/ui/toaster";

import type { AppFlashData } from "$/globals";

let isUnloading = false;

window.addEventListener("beforeunload", () => (isUnloading = true));

export function useFlashToaster(): void {
    function toast(flash: AppFlashData) {
        if (flash.error) {
            toaster.error(flash.error);
        } else if (flash.message) {
            toaster.info(flash.message);
        } else if (flash.success) {
            toaster.success(flash.success);
        }
    }

    $effect(() => router.on("flash", (e) => toast(e.detail.flash)));

    $effect(() =>
        router.on("httpException", ({ detail: { response } }) => {
            if (!isUnloading && response.status != 429) {
                console.error(response.data);
                toaster.error(m["common.unexpected-error"]());
            }
            return false;
        })
    );

    $effect(() =>
        router.on("networkError", (e) => {
            e.preventDefault();
            if (!isUnloading) {
                toaster.error(m["common.unexpected-error"]());
            }
            return false;
        })
    );
}
