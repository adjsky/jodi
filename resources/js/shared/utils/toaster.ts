import { createToaster } from "@ark-ui/svelte/toast";

export const toaster = createToaster({
    placement: "top",
    overlap: true,
    gap: 4
});
