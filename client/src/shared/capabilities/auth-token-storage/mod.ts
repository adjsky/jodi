import { SecureStorage } from "../secure-storage/mod.ts";

const AUTH_TOKEN_KEY = ["auth", "token"];

async function get(): Promise<string | null> {
    const token = await SecureStorage.get(AUTH_TOKEN_KEY);

    if (token === null) {
        return token;
    }

    if (typeof token !== "string") {
        await remove();
        return null;
    }

    return token;
}

function set(token: string): Promise<void> {
    return SecureStorage.set(AUTH_TOKEN_KEY, token);
}

function remove(): Promise<boolean> {
    return SecureStorage.remove(AUTH_TOKEN_KEY);
}

export const AuthTokenStorage = {
    get,
    set,
    remove
};
