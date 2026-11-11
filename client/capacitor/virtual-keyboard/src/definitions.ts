import type { PluginListenerHandle } from "@capacitor/core";

export type KeyboardHeight = {
    height: number;
};

export type VirtualKeyboardEvent = "heightChange";

export interface VirtualKeyboardPlugin {
    addListener(
        event: VirtualKeyboardEvent,
        listener: (state: KeyboardHeight) => void
    ): Promise<PluginListenerHandle>;
}
