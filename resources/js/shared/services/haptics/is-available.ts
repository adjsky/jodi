import { Haptics } from "@capawesome/capacitor-haptics";

let availability: boolean | null = null;

export async function isAvailable(): Promise<boolean> {
    availability ??= await Haptics.isAvailable().then(
        ({ available }) => available,
        () => false
    );

    return availability;
}
