import { page, router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";
import { toaster } from "$/shared/ui/toaster";
import { onMount } from "svelte";
import { get } from "svelte/store";

import type { AppPageProps } from "$/globals";

let isUnloading = false;

window.addEventListener("beforeunload", () => (isUnloading = true));

export function useFlashToaster(): void {
    function toast(flash: AppPageProps["flash"]) {
        if (flash.error) {
            toaster.error(flash.error);
        } else if (flash.message) {
            toaster.info(flash.message);
        } else if (flash.success) {
            toaster.success(flash.success);
        }
    }

    onMount(() => toast(get(page).props.flash));

    $effect(() =>
        router.on("success", (e) => toast(e.detail.page.props.flash))
    );

    $effect(() =>
        router.on("invalid", ({ detail: { response } }) => {
            if (!isUnloading && response.status != 429) {
                console.error(response.data.message);
                toaster.error(m["common.unexpected-error"]());
            }

            return false;
        })
    );

    $effect(() =>
        router.on("exception", (e) => {
            e.preventDefault();

            if (!isUnloading) {
                toaster.error(m["common.unexpected-error"]());
            }
        })
    );
}
