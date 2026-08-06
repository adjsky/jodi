import { toast } from "svelte-sonner";

import Toast from "./Toast.svelte";

import type { ComponentProps } from "svelte";

type ToastTypes = ComponentProps<typeof Toast>["type"];

const types = ["info", "success", "error"] as const;

export const toaster = types.reduce(
    (acc, type) => {
        acc[type] = (title, description) => {
            toast(Toast, {
                position: "top-center",
                unstyled: true,
                class: "w-full [view-transition-name:disabled] top-safe!",
                componentProps: { title, description, type }
            });
        };
        return acc;
    },
    {} as Record<ToastTypes, (title: string, description?: string) => void>
);
