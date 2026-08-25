import { Keyboard as CapKeyboard } from "@capacitor/keyboard";
import { getCaretBottomOffset } from "#shared/lib/dom/get-caret-bottom-offset.ts";
import { raf } from "#shared/lib/dom/raf.ts";
import { scrollIntoView } from "#shared/lib/dom/scroll-into-view.ts";
import { onMount } from "svelte";

import { OVERLAYS_CONTENT } from "./overlays-content.ts";
import { retainFocus } from "./retain-focus.ts";

import type { KeyboardInfo } from "@capacitor/keyboard";

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

        function updateHeight(info: KeyboardInfo) {
            document.documentElement.style.setProperty(
                "--kb-height",
                `${info.keyboardHeight}px`
            );

            raf(() => {
                revealFocusedElement();
                revealCaret();
            });
        }

        const willShowListener = CapKeyboard.addListener(
            "keyboardWillShow",
            updateHeight
        );

        const didShowListener = CapKeyboard.addListener(
            "keyboardDidShow",
            updateHeight
        );

        const hideListener = CapKeyboard.addListener("keyboardWillHide", () => {
            document.documentElement.style.setProperty("--kb-height", "0px");
        });

        return () => {
            void willShowListener.then((listener) => listener.remove());
            void didShowListener.then((listener) => listener.remove());
            void hideListener.then((listener) => listener.remove());
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
