/// <reference types="svelte" />
/// <reference types="vite/client" />
/// <reference types="vite-plugin-pwa/svelte" />

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

declare global {
    export const __VAPID_PUBLIC_KEY__: string;
}

export interface AppPageProps {
    version: string;
    search: Record<string, string>;
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            preferences: {
                locale: string;
                weekStartOn: "monday" | "sunday";
                notifications: "push" | "mail";
            };
        };
    };
    flash: {
        message: string | null;
        error: string | null;
        success: string | null;
    };
}
