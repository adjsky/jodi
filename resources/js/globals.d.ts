/// <reference types="svelte" />
/// <reference types="vite/client" />

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
            preferences: Record<string, unknown>;
        };
    };
    flash: {
        message: string | null;
        error: string | null;
        success: string | null;
    };
}
