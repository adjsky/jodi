import { Haptics } from "@capawesome/capacitor-haptics";

let availability: Promise<boolean> | undefined;

export function isAvailable(): Promise<boolean> {
    return (availability ??= Haptics.isAvailable().then(
        ({ available }) => available,
        () => false
    ));
}
