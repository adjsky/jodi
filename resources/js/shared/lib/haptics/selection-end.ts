import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-avaiable";

export async function selectionEnd() {
    if (!(await isAvailable())) return;

    void Haptics.selectionEnd();
}
