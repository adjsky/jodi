import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "#shared/cfg/constants.ts";

import { isAvailable } from "./is-available.ts";

export async function selectionChanged(): Promise<void> {
    if (!(await isAvailable()) || PLATFORM == "web") return;

    void Haptics.selectionChanged();
}
