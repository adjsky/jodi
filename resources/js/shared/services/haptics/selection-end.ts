import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-available";

export async function selectionEnd(): Promise<void> {
    if (!(await isAvailable())) return;

    void Haptics.selectionEnd();
}
