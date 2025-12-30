import type { FormDataConvertible } from "@inertiajs/core";

export function cleanFormPayload(data: Record<string, FormDataConvertible>) {
    return Object.keys(data).reduce(
        (acc, key) => {
            if (!key.startsWith("_")) {
                acc[key] = data[key];
            }
            return acc;
        },
        {} as Record<string, FormDataConvertible>
    );
}

export function announce(input: HTMLInputElement | null) {
    input?.dispatchEvent(new Event("input", { bubbles: true }));
}
