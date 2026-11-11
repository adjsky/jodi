import { VirtualKeyboard as Keyboard } from "@jodi/capacitor-virtual-keyboard";
import { raf } from "#shared/lib/dom/raf.ts";
import { onMount } from "svelte";

import { OVERLAYS_CONTENT } from "./overlays-content.ts";
import { revealCaret } from "./reveal-caret.ts";
import { revealFocusedElement } from "./reveal-focused-element.ts";

import type { KeyboardHeight } from "@jodi/capacitor-virtual-keyboard";

export function init(): void {
    onMount(() => {
        if (!OVERLAYS_CONTENT) {
            return;
        }

        function updateHeight({ height }: KeyboardHeight): void {
            document.documentElement.style.setProperty(
                "--keyboard-inset-height",
                `${height}px`
            );

            if (height > 0) {
                raf(() => {
                    revealFocusedElement();
                    revealCaret();
                });
            }
        }

        const heightChangeListener = Keyboard.addListener(
            "heightChange",
            updateHeight
        );

        return () => {
            void heightChangeListener.then((listener) => listener.remove());
        };
    });
}
