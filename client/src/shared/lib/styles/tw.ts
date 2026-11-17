import { extendTailwindMerge, validators } from "tailwind-merge";

import type { ClassValue } from "svelte/elements";

export type ClassName = ClassValue | undefined | null | false;

const safe_area_spacing = [
    "safe",
    {
        "safe-offset": [validators.isAny],
        "safe-or": [validators.isAny]
    }
];

const tw_merge = extendTailwindMerge({
    extend: {
        theme: {
            text: ["ms"]
        },
        classGroups: {
            m: [{ m: safe_area_spacing }],
            mx: [{ mx: safe_area_spacing }],
            my: [{ my: safe_area_spacing }],
            ms: [{ ms: safe_area_spacing }],
            me: [{ me: safe_area_spacing }],
            mt: [{ mt: safe_area_spacing }],
            mr: [{ mr: safe_area_spacing }],
            mb: [{ mb: safe_area_spacing }],
            ml: [{ ml: safe_area_spacing }],

            p: [{ p: safe_area_spacing }],
            px: [{ px: safe_area_spacing }],
            py: [{ py: safe_area_spacing }],
            ps: [{ ps: safe_area_spacing }],
            pe: [{ pe: safe_area_spacing }],
            pt: [{ pt: safe_area_spacing }],
            pr: [{ pr: safe_area_spacing }],
            pb: [{ pb: safe_area_spacing }],
            pl: [{ pl: safe_area_spacing }]
        }
    }
});

export const tw = tw_merge as (...args: ClassName[]) => string;
