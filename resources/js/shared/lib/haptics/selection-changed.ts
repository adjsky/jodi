import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-avaiable";

export async function selectionChanged() {
    if (!(await isAvailable())) return;

    void Haptics.selectionChanged();
}
