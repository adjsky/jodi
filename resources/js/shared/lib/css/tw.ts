import { extendTailwindMerge } from "tailwind-merge";

import type { ClassValue } from "svelte/elements";

export type ClassName = ClassValue | undefined | null | false;

const twMerge = extendTailwindMerge({
    extend: {
        theme: {
            text: ["ms"]
        }
    }
});

export const tw = twMerge as (...args: ClassName[]) => string;
