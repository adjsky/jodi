import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-avaiable";

export async function selectionEnd() {
    if (!isAvailable) return;

    void Haptics.selectionEnd();
}
