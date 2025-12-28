import { inertia } from "@inertiajs/svelte";
import { fromAction } from "svelte/attachments";

export type LinkParameters = NonNullable<Parameters<typeof inertia>[1]>;

export const link = (params: () => LinkParameters) =>
    fromAction(inertia, params);
