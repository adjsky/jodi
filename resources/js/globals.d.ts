/// <reference types="svelte" />
/// <reference types="vite/client" />
/// <reference types="vite-plugin-pwa/svelte" />

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {}

    interface InertiaConfig {
        flashDataType: AppFlashData;
    }
}

export interface AppFlashData {
    message?: string;
    error?: string;
    success?: string;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    [key: string]: any;
}

export interface AppPageProps {
    version: string;
    environment: string;
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
    config: {
        firebase: {
            apiKey: string;
            authDomain: string;
            projectId: string;
            storageBucket: string;
            messagingSenderId: string;
            appId: string;
            vapidKey: string;
        };
        reminders: {
            window: {
                days: number;
                hours: number;
                minutes: number;
            };
        };
    };
}
