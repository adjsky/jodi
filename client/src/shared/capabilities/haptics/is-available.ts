import { Haptics as CapHaptics } from "@capawesome/capacitor-haptics";
import { PLATFORM } from "#shared/cfg/constants.ts";

export let isAvailable: Promise<boolean>;

if (PLATFORM === "web") {
    isAvailable = Promise.resolve(false);
} else {
    isAvailable = CapHaptics.isAvailable().then(
        ({ available }) => available,
        () => false
    );
}
