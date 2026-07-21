import { extract } from "runed";
import { onMount } from "svelte";

import { raf } from "./dom";

import type { Getter, MaybeGetter } from "runed";

export function useLastMatching<T>(
    value: Getter<T>,
    matcher: (value: T) => boolean
) {
    const initialValue = extract(value);

    let lastMatch: T | null = $state(
        matcher(initialValue) ? initialValue : null
    );

    $effect(() => {
        const newValue = extract(value);
        if (matcher(newValue)) lastMatch = newValue;
    });

    return {
        get current() {
            return lastMatch;
        },
        reset() {
            lastMatch = null;
        }
    };
}

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
