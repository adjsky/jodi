import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";

import { toaster } from "../lib/toast";

export function useToaster() {
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
                    toaster.error({ title: flash.error });
                } else if (flash.message) {
                    toaster.info({ title: flash.message });
                } else if (flash.success) {
                    toaster.success({ title: flash.success });
                }
            }
        )
    );

    $effect(() => {
        router.on("invalid", ({ detail: { response } }) => {
            console.error(response.data.message);
            toaster.error({ title: m["common.unexpected-error"]() });
            return false;
        });
    });

    $effect(() =>
        router.on("exception", (e) => {
            e.preventDefault();
            toaster.error({ title: m["common.unexpected-error"]() });
        })
    );
}
