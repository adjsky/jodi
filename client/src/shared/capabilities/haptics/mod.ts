import {
    Haptics as CapHaptics,
    ImpactStyle as CapImpactStyle
} from "@capawesome/capacitor-haptics";
import { PLATFORM } from "#shared/cfg/constants.ts";
import { match } from "ts-pattern";

import { isAvailable } from "./is-available.ts";

export type ImpactStyle = "soft" | "rigid" | "light" | "medium" | "heavy";

async function impact(style: ImpactStyle): Promise<void> {
    if (!(await isAvailable) || PLATFORM === "web") return;

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
    if (!(await isAvailable) || PLATFORM === "web") return;

    void CapHaptics.vibrate({ duration });
}

async function selectionStart(): Promise<void> {
    if (!(await isAvailable)) return;

    void CapHaptics.selectionStart();
}

async function selectionChanged(): Promise<void> {
    if (!(await isAvailable) || PLATFORM === "web") return;

    void CapHaptics.selectionChanged();
}

async function selectionEnd(): Promise<void> {
    if (!(await isAvailable)) return;

    void CapHaptics.selectionEnd();
}

export const Haptics = {
    impact,
    vibrate,
    selectionStart,
    selectionChanged,
    selectionEnd
};
