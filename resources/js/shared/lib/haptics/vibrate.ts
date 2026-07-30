import { Haptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";

import { isAvailable } from "./is-avaiable";

export async function vibrate(duration: number) {
    if (!isAvailable || PLATFORM == "web") return;

    void Haptics.vibrate({ duration });
}
