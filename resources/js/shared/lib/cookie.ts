export function get(key: string) {
    return (
        document.cookie
            .split("; ")
            .find((row) => row.startsWith(key + "="))
            ?.split("=")[1] ?? null
    );
}

type SetCookieOptions = {
    path?: string;
    maxAge?: number;
    sameSite?: "strict" | "lax" | "none";
};

export function set(key: string, value: string, options?: SetCookieOptions) {
    const { path = "/", maxAge, sameSite } = options ?? {};

    let cookie = `${key}=${value}; path=${path};`;

    if (maxAge) {
        cookie += `max-age=${maxAge};`;
    }

    if (sameSite) {
        cookie += `samesite=${sameSite};`;
    }

    document.cookie = cookie;
}
