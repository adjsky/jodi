import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";

import { toaster } from "../lib/toast";

import type { RouteDefinition } from "$/generated/wayfinder";

export function useActionRateLimit(action: RouteDefinition<["post"]>) {
    let secondsLeft = $state(0);
    let running = $state(false);

    $effect(() =>
        router.on("invalid", (e) => {
            const { response } = e.detail;

            if (
                response.status != 429 ||
                new URL(response.request.responseURL).pathname != action.url
            ) {
                return;
            }

            e.preventDefault();

            secondsLeft = Number(response.headers["retry-after"]);
            running = true;

            toaster.error({ title: m["common.too-many-requests"]() });
        })
    );

    $effect(() => {
        if (!running) {
            return;
        }

        function tick() {
            secondsLeft -= 1;

            if (secondsLeft <= 0) {
                running = false;
            }
        }

        const id = setInterval(tick, 1000);

        return () => {
            clearInterval(id);
        };
    });

    return {
        get secondsLeft() {
            return secondsLeft;
        },
        get running() {
            return running;
        }
    };
}
