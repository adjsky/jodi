import { Haptics } from "@capawesome/capacitor-haptics";

export async function isAvailable() {
    const { available } = await Haptics.isAvailable();
    return available;
}
