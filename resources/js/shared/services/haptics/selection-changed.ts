import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";

import { isAvailable } from "./is-available";

export function selectionChanged(): void {
    if (!isAvailable || PLATFORM == "web") return;

    void Haptics.selectionChanged();
}
