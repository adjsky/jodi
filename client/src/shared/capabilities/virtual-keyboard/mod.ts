import { init } from "./init.ts";
import { OVERLAYS_CONTENT } from "./overlays-content.ts";
import { retainFocus } from "./retain-focus.ts";

export const VirtualKeyboard = {
    init,
    retainFocus,
    get overlaysContent(): boolean {
        return OVERLAYS_CONTENT;
    }
};
