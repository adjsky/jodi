import { getCaretBottomOffset } from "$/shared/lib/dom/get-caret-bottom-offset";

export function revealCaret(): void {
    if (!(document.activeElement instanceof HTMLTextAreaElement)) return;

    const textarea = document.activeElement;
    const caretBottomOffset = getCaretBottomOffset(textarea);

    const delta = Math.max(caretBottomOffset - textarea.clientHeight, 0);

    textarea.scrollBy({ top: delta, behavior: "instant" });
}
