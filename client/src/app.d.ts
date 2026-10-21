// See https://kit.svelte.dev/docs/types#app

import type { OverlayState } from "#shared/integrations/kit/mod.ts";

// for information about these interfaces
declare global {
    namespace App {
        // interface Error {}
        // interface Locals {}
        // interface PageData {}
        // interface Platform {}

        interface PageState {
            overlay?: OverlayState;
        }
    }

    interface Window {
        __JODI_APP_CONFIG__: unknown;
        __TANSTACK_QUERY_CLIENT__: import("@tanstack/query-core").QueryClient;
    }
}

export {};
