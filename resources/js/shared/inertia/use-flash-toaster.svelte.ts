import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";

import { toaster } from "../lib/toaster";

let isUnloading = false;

window.addEventListener("beforeunload", () => (isUnloading = true));

export function useFlashToaster() {
    $effect(() =>
        router.on("success", (e) => {
            const { flash } = e.detail.page.props;

            if (flash.error) {
                toaster.error(flash.error);
            } else if (flash.message) {
                toaster.info(flash.message);
            } else if (flash.success) {
                toaster.success(flash.success);
            }
        })
    );

    $effect(() => {
        router.on("invalid", ({ detail: { response } }) => {
            if (!isUnloading && response.status != 429) {
                console.error(response.data.message);
                toaster.error(m["common.unexpected-error"]());
            }

            return false;
        });
    });

    $effect(() =>
        router.on("exception", (e) => {
            e.preventDefault();

            if (!isUnloading) {
                toaster.error(m["common.unexpected-error"]());
            }
        })
    );
}
