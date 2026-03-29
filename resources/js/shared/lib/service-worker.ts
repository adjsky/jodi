import { progress } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";
import { watch } from "runed";
import { onMount } from "svelte";
import { fromStore } from "svelte/store";
import { useRegisterSW } from "virtual:pwa-register/svelte";

import { createActionBanner } from "../ui/ActionBanner.svelte";

export function useServiceWorker() {
    const { needRefresh: _needRefresh, updateServiceWorker } = useRegisterSW();
    const needRefresh = fromStore(_needRefresh);

    watch(
        () => [needRefresh.current],
        () => {
            if (!needRefresh.current) {
                return;
            }

            createActionBanner(m["pwa-update.title"](), {
                action: m["pwa-update.action"](),
                onAccept() {
                    progress.reveal(true);
                    progress.start();
                    void updateServiceWorker();
                },
                autoclose: false
            });
        }
    );

    onMount(() => {
        if (!("serviceWorker" in navigator)) return;

        function handler(event: MessageEvent) {}

        navigator.serviceWorker.addEventListener("message", handler);

        return () =>
            navigator.serviceWorker.removeEventListener("message", handler);
    });
}
