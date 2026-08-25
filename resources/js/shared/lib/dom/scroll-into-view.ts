import { compute } from "compute-scroll-into-view";

type Options = {
    block?: ScrollLogicalPosition;
    inline?: ScrollLogicalPosition;
    behavior?: ScrollBehavior;
};

// TODO: Use native scrollIntoWith with container=nearest when it will be
//       supported in all major browsers.
export function scrollIntoView(target: Element, options?: Options): void {
    const { block, inline, behavior = "instant" } = options ?? {};

    const actions = compute(target, {
        scrollMode: "if-needed",
        skipOverflowHiddenElements: true,
        block,
        inline
    });

    const { el, left, top } = actions[0];

    el.scrollTo({ left, top, behavior });
}
