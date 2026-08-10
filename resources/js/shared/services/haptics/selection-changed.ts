import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";

import { isAvailable } from "./is-available";

export async function selectionChanged(): Promise<void> {
    if (!(await isAvailable()) || PLATFORM == "web") return;

    void Haptics.selectionChanged();
}
