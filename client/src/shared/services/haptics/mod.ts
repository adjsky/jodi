import { impact } from "./impact.ts";
import { selectionChanged } from "./selection-changed.ts";
import { selectionEnd } from "./selection-end.ts";
import { selectionStart } from "./selection-start.ts";
import { vibrate } from "./vibrate.ts";

export const Haptics = {
    impact,
    vibrate,
    selectionStart,
    selectionChanged,
    selectionEnd
};
