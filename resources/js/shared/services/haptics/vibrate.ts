import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";

import { isAvailable } from "./is-available";

export async function vibrate(duration: number): Promise<void> {
    if (!isAvailable || PLATFORM == "web") return;

    void Haptics.vibrate({ duration });
}
