import { m } from "#paraglide/messages.js";
import { toaster } from "#shared/ui/toaster.ts";
import { onMount } from "svelte";

let toast_id: string | number | null = null;

function handle_online() {
    if (!toast_id) return;

    toaster.drop(toast_id);
    toaster.success(m["common.network.online"]());
}

function handle_offline() {
    if (toast_id) return;
    toast_id = toaster.error(m["common.network.offline"]());
}

export function init(): void {
    onMount(() => {
        if (!navigator.onLine) {
            handle_offline();
        }

        window.addEventListener("online", handle_online);

        return window.addEventListener("online", handle_offline);
    });
}
