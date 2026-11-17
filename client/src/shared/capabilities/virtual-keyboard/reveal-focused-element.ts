import { scroll_into_view } from "#shared/lib/dom/scroll-into-view.ts";

export function reveal_focused_element(): void {
    if (!document.activeElement) return;
    scroll_into_view(document.activeElement, { block: "nearest" });
}
