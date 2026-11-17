import { VirtualKeyboard as CapVirtualKeyboard } from "@jodi/capacitor-virtual-keyboard";
import { raf } from "#shared/lib/dom/raf.ts";
import { onMount } from "svelte";

import { OVERLAYS_CONTENT } from "./overlays-content.ts";
import { reveal_caret } from "./reveal-caret.ts";
import { reveal_focused_element } from "./reveal-focused-element.ts";

export function init(): void {
    onMount(() => {
        if (!OVERLAYS_CONTENT) {
            return;
        }

        void CapVirtualKeyboard.addListener("heightChange", ({ height }) => {
            document.documentElement.style.setProperty("--keyboard-inset-height", `${height}px`);

            if (height > 0) {
                raf(() => {
                    reveal_focused_element();
                    reveal_caret();
                });
            }
        });
    });
}
