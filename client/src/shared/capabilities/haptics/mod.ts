import {
    Haptics as CapHaptics,
    ImpactStyle as CapImpactStyle
} from "@capawesome/capacitor-haptics";
import { PLATFORM } from "#shared/cfg/capacitor.ts";
import { match } from "ts-pattern";

import { is_available } from "./is-available.ts";

export type ImpactStyle = "soft" | "rigid" | "light" | "medium" | "heavy";

async function impact(style: ImpactStyle): Promise<void> {
    if (!(await is_available) || PLATFORM === "web") return;

    void CapHaptics.impact({
        style: match(style)
            .with("soft", () => CapImpactStyle.Soft)
            .with("rigid", () => CapImpactStyle.Rigid)
            .with("light", () => CapImpactStyle.Light)
            .with("medium", () => CapImpactStyle.Medium)
            .with("heavy", () => CapImpactStyle.Heavy)
            .exhaustive()
    });
}

async function vibrate(duration: number): Promise<void> {
    if (!(await is_available) || PLATFORM === "web") return;

    void CapHaptics.vibrate({ duration });
}

async function selection_start(): Promise<void> {
    if (!(await is_available)) return;

    void CapHaptics.selectionStart();
}

async function selection_changed(): Promise<void> {
    if (!(await is_available) || PLATFORM === "web") return;

    void CapHaptics.selectionChanged();
}

async function selection_end(): Promise<void> {
    if (!(await is_available)) return;

    void CapHaptics.selectionEnd();
}

export const Haptics = {
    impact,
    vibrate,
    selection_start,
    selection_changed,
    selection_end
};
