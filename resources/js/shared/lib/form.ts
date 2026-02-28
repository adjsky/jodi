import type { Arrayable } from "type-fest";

export function announce(inputs: Arrayable<HTMLInputElement | null>) {
    for (const input of Array.isArray(inputs) ? inputs : [inputs]) {
        input?.dispatchEvent(new Event("input", { bubbles: true }));
    }
}
