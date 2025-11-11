import { twMerge } from "tailwind-merge";

import type { ClassValue } from "svelte/elements";
import type { ClassNameValue } from "tailwind-merge";
import type { Except } from "type-fest";

export type ClassName = ClassNameValue;

export type WithClassName<
    T extends { class?: ClassValue | null },
    E = unknown
> = Except<T, "class"> & {
    class?: ClassName;
} & E;

export function tw(...classes: ClassNameValue[]) {
    return twMerge(classes);
}
