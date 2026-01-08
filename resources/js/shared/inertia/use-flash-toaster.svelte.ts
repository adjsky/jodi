import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";

import { toaster } from "../lib/toaster";

export function useFlashToaster() {
    $effect(() =>
        router.on(
            "success",
            ({
                detail: {
                    page: {
                        props: { flash }
                    }
                }
            }) => {
                if (flash.error) {
                    toaster.error(flash.error);
                } else if (flash.message) {
                    toaster.info(flash.message);
                } else if (flash.success) {
                    toaster.success(flash.success);
                }
            }
        )
    );

    $effect(() => {
        router.on("invalid", ({ detail: { response } }) => {
            console.error(response.data.message);
            toaster.error(m["common.unexpected-error"]());
            return false;
        });
    });

    $effect(() =>
        router.on("exception", (e) => {
            e.preventDefault();
            toaster.error(m["common.unexpected-error"]());
        })
    );
}
