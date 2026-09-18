import { scrollIntoView } from "#shared/lib/dom/scroll-into-view.ts";

export function revealFocusedElement(): void {
    if (!document.activeElement) return;

    scrollIntoView(document.activeElement, { block: "nearest" });
}
