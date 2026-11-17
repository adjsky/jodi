import { get_caret_bottom_offset } from "#shared/lib/dom/get-caret-bottom-offset.ts";

export function reveal_caret(): void {
    if (!(document.activeElement instanceof HTMLTextAreaElement)) return;

    const textarea = document.activeElement;
    const caret_bottom_offset = get_caret_bottom_offset(textarea);
    const delta = Math.max(caret_bottom_offset - textarea.clientHeight, 0);

    textarea.scrollBy({ top: delta, behavior: "instant" });
}
