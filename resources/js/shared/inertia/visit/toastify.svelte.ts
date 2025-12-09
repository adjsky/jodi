import { page } from "@inertiajs/svelte";
import { onMount } from "svelte";
import { get } from "svelte/store";

import { toaster } from "../../lib/toast";

import type { VisitCallbacks } from "@inertiajs/core";
import type { AppPageProps } from "$/globals";

type Options = {
    invalid?: string;
};

export function toastify(options?: Options): Partial<VisitCallbacks> {
    return {
        onSuccess({ props: { flash } }) {
            handle(flash);
        },
        onInvalid(response) {
            toaster.error({ title: options?.invalid ?? response.data.message });
            return false;
        }
    };
}

export function useInitialToast() {
    onMount(() => {
        handle(get(page).props.flash);
    });
}

function handle(flash: AppPageProps["flash"]) {
    if (flash.error) {
        toaster.error({ title: flash.error });
    } else if (flash.message) {
        toaster.info({ title: flash.message });
    } else if (flash.success) {
        toaster.success({ title: flash.success });
    }
}
