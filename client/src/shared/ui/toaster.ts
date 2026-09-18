import { toast } from "svelte-sonner";

import Toast from "./Toast.svelte";

import type { ComponentProps } from "svelte";

type ToastTypes = ComponentProps<typeof Toast>["type"];

const TYPES = ["info", "success", "error"] as const;

export const toaster = TYPES.reduce(
    (acc, type) => {
        acc[type] = (title, description) => {
            toast(Toast, {
                position: "top-center",
                unstyled: true,
                class: "w-full [view-transition-name:disabled] top-safe! pointer-events-auto",
                componentProps: { title, description, type }
            });
        };
        return acc;
    },
    {} as Record<ToastTypes, (title: string, description?: string) => void>
);
