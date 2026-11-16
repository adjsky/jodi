import { Capacitor } from "@capacitor/core";
import { Device } from "@capacitor/device";

export const PLATFORM = Capacitor.getPlatform();

export const DEVICE_ID = await Device.getId().then((d) => d.identifier);
