import { m } from "#paraglide/messages.js";
import { toaster } from "#shared/ui/toaster.ts";
import { onMount } from "svelte";

let toastId: string | number | null = null;

function handleOnline() {
    if (!toastId) return;

    toaster.drop(toastId);
    toaster.success(m["common.network.online"]());
}

function handleOffline() {
    if (toastId) return;
    toastId = toaster.error(m["common.network.offline"]());
}

export function init(): void {
    onMount(() => {
        if (!navigator.onLine) {
            handleOffline();
        }

        window.addEventListener("online", handleOnline);

        return window.addEventListener("online", handleOffline);
    });
}
