import {
    ImpactStyle as CapImpactStyle,
    Haptics
} from "@capawesome/capacitor-haptics";
import { PLATFORM } from "$/shared/cfg/constants";
import { match } from "ts-pattern";

import { isAvailable } from "./is-available";

type ImpactStyle = "soft" | "rigid" | "light" | "medium" | "heavy";

export async function impact(style: ImpactStyle): Promise<void> {
    if (!isAvailable || PLATFORM == "web") return;

    void Haptics.impact({
        style: match(style)
            .with("soft", () => CapImpactStyle.Soft)
            .with("rigid", () => CapImpactStyle.Rigid)
            .with("light", () => CapImpactStyle.Light)
            .with("medium", () => CapImpactStyle.Medium)
            .with("heavy", () => CapImpactStyle.Heavy)
            .exhaustive()
    });
}
