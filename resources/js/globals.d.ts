/// <reference types="svelte" />
/// <reference types="vite/client" />
/// <reference types="vite-plugin-pwa/svelte" />

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

export interface AppPageProps {
    search: Record<string, string>;
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            preferences: {
                locale: string;
                weekStartOn: "monday" | "sunday";
            };
        };
    };
    flash: {
        message: string | null;
        error: string | null;
        success: string | null;
    };
}
