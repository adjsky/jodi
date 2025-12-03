import { inertia } from "@inertiajs/svelte";
import { fromAction } from "svelte/attachments";

type ActionParameters = NonNullable<Parameters<typeof inertia>[1]>;

export const link = (params: () => ActionParameters) =>
    fromAction(inertia, params);
