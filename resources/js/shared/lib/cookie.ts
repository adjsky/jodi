export function get(key: string) {
    return (
        document.cookie
            .split("; ")
            .find((row) => row.startsWith(key + "="))
            ?.split("=")[1] ?? null
    );
}

export function csrf() {
    return decodeURIComponent(get("XSRF-TOKEN")!);
}

type SetCookieOptions = {
    path?: string;
    maxAge?: number;
    sameSite?: "strict" | "lax" | "none";
    secure?: boolean;
};

export function set(key: string, value: string, options?: SetCookieOptions) {
    const { path = "/", maxAge, sameSite, secure } = options ?? {};

    let cookie = `${key}=${value}; path=${path};`;

    if (maxAge) {
        cookie += `max-age=${maxAge};`;
    }

    if (sameSite) {
        cookie += `samesite=${sameSite};`;
    }

    if (secure) {
        cookie += "secure;";
    }

    document.cookie = cookie;
}
