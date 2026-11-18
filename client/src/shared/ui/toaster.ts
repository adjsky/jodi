import { toast } from "svelte-sonner";

import Toast from "./Toast.svelte";

import type { ComponentProps } from "svelte";

type ToastTypes = ComponentProps<typeof Toast>["type"];
type ToastOptions = {
    description?: string;
    action?: { label: string; onclick?: VoidFunction };
};

const TYPES = ["info", "success", "error"] as const;

export const toaster = {
    drop(id: string | number): void {
        toast.dismiss(id);
    },
    ...TYPES.reduce(
        (acc, type) => {
            acc[type] = (title, options) => {
                const { description, action } = options ?? {};

                const id = toast(Toast, {
                    position: "top-center",
                    unstyled: true,
                    class: "w-full [view-transition-name:disabled] top-safe! pointer-events-auto",
                    componentProps: {
                        title,
                        description,
                        type,
                        action: action
                            ? {
                                  ...action,
                                  onclick() {
                                      toast.dismiss(id);
                                      action.onclick?.();
                                  }
                              }
                            : undefined
                    }
                });

                return id;
            };
            return acc;
        },
        {} as Record<ToastTypes, (title: string, options?: ToastOptions) => string | number>
    )
};
