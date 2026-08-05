import { extract } from "runed";
import { onMount } from "svelte";

import { raf } from "../dom/raf";

import type { MaybeGetter } from "runed";

export class DeferUntilNextFrame {
    #frames: MaybeGetter<number>;

    #frame = $state(0);

    constructor(frames: MaybeGetter<number>) {
        this.#frames = frames;

        this.#init();
    }

    #init() {
        onMount(() => {
            let cancel: VoidFunction | null = null;

            const next = () => {
                if (this.#frame >= extract(this.#frames)) return;

                cancel = raf(() => {
                    this.#frame += 1;
                    next();
                });
            };

            next();

            return () => cancel?.();
        });
    }

    get frame(): number {
        return this.#frame;
    }

    get ready(): boolean {
        return this.#frame >= extract(this.#frames);
    }
}
