import { Keyboard as CapKeyboard } from "@capacitor/keyboard";
import { getCaretBottomOffset } from "#shared/lib/dom/get-caret-bottom-offset.ts";
import { raf } from "#shared/lib/dom/raf.ts";
import { scrollIntoView } from "#shared/lib/dom/scroll-into-view.ts";
import { onMount } from "svelte";

import { OVERLAYS_CONTENT } from "./overlays-content.ts";
import { retainFocus } from "./retain-focus.ts";

function init(): void {
    onMount(() => {
        if (!OVERLAYS_CONTENT) {
            return;
        }

        function revealCaret() {
            if (!(document.activeElement instanceof HTMLTextAreaElement))
                return;

            const textarea = document.activeElement;
            const caretBottomOffset = getCaretBottomOffset(textarea);

            const delta = Math.max(
                caretBottomOffset - textarea.clientHeight,
                0
            );

            textarea.scrollBy({ top: delta, behavior: "instant" });
        }

        function revealFocusedElement() {
            if (!document.activeElement) return;

            scrollIntoView(document.activeElement, { block: "nearest" });
        }

        const didShowListener = CapKeyboard.addListener(
            "keyboardDidShow",
            () => {
                raf(() => {
                    revealFocusedElement();
                    revealCaret();
                });
            }
        );

        return () => {
            void didShowListener.then((listener) => listener.remove());
        };
    });
}

export const VirtualKeyboard = {
    init,
    retainFocus,
    get overlaysContent(): boolean {
        return OVERLAYS_CONTENT;
    }
};
