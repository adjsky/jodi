import { toast } from "svelte-sonner";

import Toast from "../ui/Toast.svelte";

import type { ComponentProps } from "svelte";

type ToastTypes = ComponentProps<typeof Toast>["type"];
const types = ["info", "success", "error"] as const;

export const toaster = types.reduce(
    (acc, type) => {
        acc[type] = (title: string) => {
            toast(Toast, {
                position: "top-center",
                unstyled: true,
                duration: Infinity,
                class: "w-full [view-transition-name:disabled] top-safe!",
                componentProps: { title, type }
            });
        };
        return acc;
    },
    {} as Record<ToastTypes, (title: string) => void>
);
