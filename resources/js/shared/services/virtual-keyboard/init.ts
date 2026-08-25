import { Keyboard } from "@capacitor/keyboard";
import { raf } from "$/shared/lib/dom/raf";
import { onMount } from "svelte";

import { overlaysContent } from "./overlays-content";
import { revealFocusedElement } from "./reveal-focused-element";

import type { KeyboardInfo } from "@capacitor/keyboard";

export function init(): void {
    onMount(() => {
        if (!overlaysContent()) {
            return;
        }

        function updateHeight(info: KeyboardInfo) {
            document.documentElement.style.setProperty(
                "--kb-height",
                `${info.keyboardHeight}px`
            );

            raf(() => {
                revealFocusedElement();
            });
        }

        const willShowListener = Keyboard.addListener(
            "keyboardWillShow",
            updateHeight
        );

        const didShowListener = Keyboard.addListener(
            "keyboardDidShow",
            updateHeight
        );

        const hideListener = Keyboard.addListener("keyboardWillHide", () => {
            document.documentElement.style.setProperty("--kb-height", "0px");
        });

        return () => {
            void willShowListener.then((listener) => listener.remove());
            void didShowListener.then((listener) => listener.remove());
            void hideListener.then((listener) => listener.remove());
        };
    });
}
