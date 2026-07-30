import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";

import { isAvailable } from "./is-avaiable";

export function selectionChanged() {
    if (!isAvailable || PLATFORM == "web") return;

    void Haptics.selectionChanged();
}
