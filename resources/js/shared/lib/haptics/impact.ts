import {
    ImpactStyle as CapImpactStyle,
    Haptics
} from "@capawesome/capacitor-haptics";
import { match } from "ts-pattern";

import { isAvailable } from "./is-avaiable";

type ImpactStyle = "soft" | "rigid" | "light" | "medium" | "heavy";

export async function impact(style: ImpactStyle) {
    if (!(await isAvailable())) return;

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
