import { init } from "./init.ts";
import { OVERLAYS_CONTENT } from "./overlays-content.ts";
import { retain_focus } from "./retain-focus.ts";

export const VirtualKeyboard = {
    init,
    retain_focus,
    get overlays_content(): boolean {
        return OVERLAYS_CONTENT;
    }
};
