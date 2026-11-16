import { Haptics as CapHaptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "#shared/cfg/capacitor.ts";

export let isAvailable: Promise<boolean>;

if (PLATFORM === "web") {
    isAvailable = Promise.resolve(false);
} else {
    isAvailable = CapHaptics.isAvailable().then(
        ({ available }) => available,
        () => false
    );
}
