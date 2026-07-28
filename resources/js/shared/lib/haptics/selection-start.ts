import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-avaiable";

export async function selectionStart() {
    if (!(await isAvailable())) return;

    void Haptics.selectionStart();
}
