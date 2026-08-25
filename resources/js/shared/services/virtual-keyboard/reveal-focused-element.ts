import { scrollIntoView } from "$/shared/lib/dom/scroll-into-view";

export function revealFocusedElement(): void {
    if (!document.activeElement) return;

    scrollIntoView(document.activeElement, { block: "nearest" });
}
