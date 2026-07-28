import { Haptics } from "@capawesome/capacitor-haptics";

import { isAvailable } from "./is-avaiable";

export async function vibrate(duration: number) {
    if (!isAvailable) return;

    void Haptics.vibrate({ duration });
}
