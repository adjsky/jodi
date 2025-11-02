/// <reference types="svelte" />
/// <reference types="vite/client" />

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

export interface AppPageProps {
    flash: {
        message: string | null;
        error: string | null;
        success: string | null;
    };
}
