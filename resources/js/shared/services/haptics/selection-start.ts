import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-available";

export async function selectionStart(): Promise<void> {
    if (!(await isAvailable())) return;

    void Haptics.selectionStart();
}
