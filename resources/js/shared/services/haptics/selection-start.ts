import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-available";

export function selectionStart(): void {
    if (!isAvailable) return;

    void Haptics.selectionStart();
}
