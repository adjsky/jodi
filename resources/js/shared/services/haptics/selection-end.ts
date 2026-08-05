import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-available";

export function selectionEnd(): void {
    if (!isAvailable) return;

    void Haptics.selectionEnd();
}
