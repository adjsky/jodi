import { registerPlugin } from "@capacitor/core";

import type { VirtualKeyboardPlugin } from "./definitions.ts";

export const VirtualKeyboard =
    registerPlugin<VirtualKeyboardPlugin>("VirtualKeyboard");

export type * from "./definitions.ts";
