import { twMerge } from "tailwind-merge";

import type { ClassValue } from "svelte/elements";

export type ClassName = ClassValue | undefined | null | false;

export const tw = twMerge as (...args: ClassName[]) => string;
