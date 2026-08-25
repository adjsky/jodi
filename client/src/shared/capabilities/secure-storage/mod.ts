import { SecureStorage as CapSecureStorage } from "@aparajita/capacitor-secure-storage";

import type { DataType } from "@aparajita/capacitor-secure-storage";

async function init(): Promise<void> {
    await CapSecureStorage.setKeyPrefix("jodi:");
}

function get(key: string[]): Promise<DataType | null> {
    return CapSecureStorage.get(buildKey(...key), false);
}

function set(key: string[], data: DataType): Promise<void> {
    return CapSecureStorage.set(buildKey(...key), data, false);
}

function remove(key: string[]): Promise<boolean> {
    return CapSecureStorage.remove(buildKey(...key));
}

function buildKey(...key: string[]) {
    return key.join(".");
}

export const SecureStorage = {
    init,
    get,
    set,
    remove
};
