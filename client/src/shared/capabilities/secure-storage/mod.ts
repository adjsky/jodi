import { SecureStorage as CapSecureStorage } from "@aparajita/capacitor-secure-storage";

import type { DataType } from "@aparajita/capacitor-secure-storage";

async function init(): Promise<void> {
    await CapSecureStorage.setKeyPrefix("jodi:");
}

function get(key: string[]): Promise<DataType | null> {
    return CapSecureStorage.get(build_key(...key), false);
}

function set(key: string[], data: DataType): Promise<void> {
    return CapSecureStorage.set(build_key(...key), data, false);
}

function remove(key: string[]): Promise<boolean> {
    return CapSecureStorage.remove(build_key(...key));
}

function build_key(...key: string[]) {
    return key.join(".");
}

export const SecureStorage = {
    init,
    get,
    set,
    remove
};
