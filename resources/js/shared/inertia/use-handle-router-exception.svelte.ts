import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";

import { toaster } from "../lib/toast";

export function useHandleRouterException() {
    $effect(() =>
        router.on("exception", (e) => {
            e.preventDefault();
            toaster.error({ title: m["router-exception"]() });
        })
    );
}
