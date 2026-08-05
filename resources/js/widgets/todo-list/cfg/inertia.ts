import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["todos", "categories"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};
