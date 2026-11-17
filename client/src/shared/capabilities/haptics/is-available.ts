import { Haptics as CapHaptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "#shared/cfg/capacitor.ts";

export let is_available: Promise<boolean>;

if (PLATFORM === "web") {
    is_available = Promise.resolve(false);
} else {
    is_available = CapHaptics.isAvailable().then(
        ({ available }) => available,
        () => false
    );
}
