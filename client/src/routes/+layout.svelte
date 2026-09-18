<script lang="ts">
    import "../app.css";

    import { Portal } from "@ark-ui/svelte";
    import { SplashScreen } from "@capacitor/splash-screen";
    import { QueryClient, QueryClientProvider } from "@tanstack/svelte-query";
    import { m } from "#paraglide/messages.js";
    import { Swiper } from "#shared/integrations/swiper/mod.ts";
    import { VirtualKeyboard } from "#shared/services/virtual-keyboard/mod.ts";
    import { onMount } from "svelte";
    import { Toaster } from "svelte-sonner";

    const { children } = $props();

    const queryClient = new QueryClient({
        defaultOptions: {
            queries: {
                staleTime: 60_000,
                gcTime: 30 * 60_000,
                retry: false,
                refetchOnWindowFocus: false,
                refetchOnReconnect: true
            },
            mutations: {
                retry: false
            }
        }
    });

    onMount(() => {
        void SplashScreen.hide();
    });

    Swiper.init();
    VirtualKeyboard.init();
</script>

<svelte:head>
    <title>{m["common.app-title"]()}</title>
</svelte:head>

<QueryClientProvider client={queryClient}>
    <Portal>
        <Toaster />
    </Portal>

    {@render children()}

    <div id="portal-root"></div>
</QueryClientProvider>
