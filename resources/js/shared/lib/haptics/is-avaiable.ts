import { Haptics } from "@capawesome/capacitor-haptics";

const { available: isAvailable } = await Haptics.isAvailable();

export { isAvailable };
