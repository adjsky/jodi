import { extendTailwindMerge, validators } from "tailwind-merge";

import type { ClassValue } from "svelte/elements";

export type ClassName = ClassValue | undefined | null | false;

const safeAreaSpacing = [
    "safe",
    {
        "safe-offset": [validators.isAny],
        "safe-or": [validators.isAny]
    }
];

const twMerge = extendTailwindMerge({
    extend: {
        theme: {
            text: ["ms"]
        },
        classGroups: {
            m: [{ m: safeAreaSpacing }],
            mx: [{ mx: safeAreaSpacing }],
            my: [{ my: safeAreaSpacing }],
            ms: [{ ms: safeAreaSpacing }],
            me: [{ me: safeAreaSpacing }],
            mt: [{ mt: safeAreaSpacing }],
            mr: [{ mr: safeAreaSpacing }],
            mb: [{ mb: safeAreaSpacing }],
            ml: [{ ml: safeAreaSpacing }],

            p: [{ p: safeAreaSpacing }],
            px: [{ px: safeAreaSpacing }],
            py: [{ py: safeAreaSpacing }],
            ps: [{ ps: safeAreaSpacing }],
            pe: [{ pe: safeAreaSpacing }],
            pt: [{ pt: safeAreaSpacing }],
            pr: [{ pr: safeAreaSpacing }],
            pb: [{ pb: safeAreaSpacing }],
            pl: [{ pl: safeAreaSpacing }]
        }
    }
});

export const tw = twMerge as (...args: ClassName[]) => string;
