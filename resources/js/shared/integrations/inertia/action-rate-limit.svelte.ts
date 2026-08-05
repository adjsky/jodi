import { router } from "@inertiajs/svelte";
import { m } from "$/paraglide/messages";
import { toaster } from "$/shared/ui/toaster";

import type { RouteDefinition } from "$/generated/wayfinder";

type Method = "post" | "put" | "patch" | "delete";

export class ActionRateLimit<T extends Method | Method[]> {
    #action: RouteDefinition<T>;
    #secondsLeft = $state(0);
    #isRunning = $state(false);

    constructor(action: RouteDefinition<T>) {
        this.#action = action;

        this.#init();
    }

    #init() {
        $effect(() =>
            router.on("invalid", (e) => {
                const { response } = e.detail;

                if (
                    response.status != 429 ||
                    new URL(response.request.responseURL).pathname !=
                        this.#action.url
                ) {
                    return;
                }

                e.preventDefault();

                this.#secondsLeft = Number(response.headers["retry-after"]);
                this.#isRunning = true;

                toaster.error(m["common.too-many-requests"]());
            })
        );

        $effect(() => {
            if (!this.#isRunning) {
                return;
            }

            const tick = () => {
                this.#secondsLeft -= 1;

                if (this.#secondsLeft <= 0) {
                    this.#isRunning = false;
                }
            };

            const id = setInterval(tick, 1000);

            return () => {
                clearInterval(id);
            };
        });
    }

    get secondsLeft(): number {
        return this.#secondsLeft;
    }

    get isRunning(): boolean {
        return this.#isRunning;
    }
}
