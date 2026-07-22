import { extract } from "runed";
import { onMount } from "svelte";

import { raf } from "../dom/raf";

import type { MaybeGetter } from "runed";

export function useDeferUntilNextFrame(frames: MaybeGetter<number>) {
    let frame = $state(0);

    onMount(() => {
        let cancel: VoidFunction | null = null;

        function next() {
            if (frame >= extract(frames)) return;

            cancel = raf(() => {
                frame += 1;
                next();
            });
        }

        next();

        return () => cancel?.();
    });

    return {
        get frame() {
            return frame;
        },
        get ready() {
            return frame >= extract(frames);
        }
    };
}
