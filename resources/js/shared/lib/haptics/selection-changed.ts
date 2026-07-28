import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";

import { isAvailable } from "./is-avaiable";

export function selectionChanged() {
    if (!isAvailable) return;

    if (PLATFORM == "web") {
        navigator.vibrate?.(10);
    } else {
        void Haptics.selectionChanged();
    }
}
