/// <reference types="svelte" />
/// <reference types="vite/client" />
/// <reference types="vite-plugin-pwa/svelte" />

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

export interface AppPageProps {
    version: string;
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
        fcm: {
            token: string;
        } | null;
    };
    flash: {
        message: string | null;
        error: string | null;
        success: string | null;
    };
}
