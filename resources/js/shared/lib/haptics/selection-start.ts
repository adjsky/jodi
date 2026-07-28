import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-avaiable";

export async function selectionStart() {
    if (!isAvailable) return;

    void Haptics.selectionStart();
}
