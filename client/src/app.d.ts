import type { OverlayState } from "#shared/integrations/kit/mod.ts";

declare global {
    namespace App {
        // See https://kit.svelte.dev/docs/types#app
        // for information about these interfaces

        // interface Error {}
        // interface Locals {}
        // interface PageData {}
        // interface Platform {}

        interface PageState {
            overlay?: OverlayState;
            skip_view_transition?: boolean;
        }
    }

    interface Window {
        __JODI_APP_CONFIG__: unknown;
        __TANSTACK_QUERY_CLIENT__: import("@tanstack/query-core").QueryClient;
    }
}

export {};
